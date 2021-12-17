<?php
namespace Drnkwati\Bootpack\Commands;

use Drnkwati\Bootpack\Classes\Helpers;
use Drnkwati\Bootpack\Classes\Package;
use Illuminate\Console\Command;

class BootpackCreatePackage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bootpack:create {name : Package name} {--source= : Template location} {--path : Location to create the package}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new package';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = explode('/', $this->argument('name'));

        $pName = $name[1];

        $name = $name[0] . '/' . $name[1];

        $source = $this->sourcePath($this->option('source') ?: config('bootpack.source_path'));

        $path = $this->option('path') ? base_path($this->option('path')) : base_path(config('bootpack.base_path'));

        $path = $path . '/' . $name;

        $this->comment('The package root will be: ' . $path);
        if ($this->confirm('The package creation is going to start, type yes to begin', 'yes')) {
            if (!is_dir($path)) {
                $this->comment('Creating the root folder...');
                mkdir($path, 0777, true);
                $this->info("Project root created at: {$path}");
                $this->comment('Collecting package information...');

                $package = $this->package($name);

                $this->comment('Great, confirm the following data before we go ahead!');

                $this->table(array_keys(get_object_vars($package)), [get_object_vars($package)]);

                while (!$this->confirm('Everything looks cool?', 'yes')) {
                    $this->comment('Woah! Let me ask you everything again!');

                    $package = $this->package($name);

                    $this->comment('Great, confirm the following data before we go ahead!');

                    $this->table(array_keys(get_object_vars($package)), [get_object_vars($package)]);
                };

                $this->comment('Fantastic! Let me create the composer.json for you...');

                file_put_contents($path . '/composer.json', $package->json());

                $this->info("The package configuration has been saved in " . $path . '/composer.json');

                $this->comment('Woah, this is getting dirty, lets create the package structure');

                Helpers::copyDir($source, $path);

                $this->info('Package structure created');

                $this->comment("Things are going fast artisan, let's add your package information into them");

                Helpers::massStrReplaceFile('{{ NAMESPACE }}', $package->namespace, $path);
                Helpers::massStrReplaceFile('{{ NAME }}', $pName, $path);
                Helpers::massStrReplaceFile('{{ UCNAME }}', ucfirst($pName), $path);

                // rename files
                $srcFile = $path . '/src/Config/config.php';
                $desFile = $path . '/src/Config/' . $pName . '.php';
                if (!is_file($srcFile)) {
                    $srcFile = $path . '/config/config.php';
                    $desFile = $path . '/config/' . $pName . '.php';
                }
                !is_file($srcFile) ?: rename($srcFile, $desFile);

                !is_file($srcFile = $path . '/src/ServiceProvider.php') ?: rename(
                    $srcFile, $path . '/src/' . ucfirst($pName) . 'ServiceProvider.php'
                );
                !is_file($srcFile = $path . '/src/Controllers/Controller.php') ?: rename(
                    $srcFile, $path . '/src/Controllers/' . ucfirst($pName) . 'Controller.php'
                );
                !is_file($srcFile = $path . '/src/Commands/Command.php') ?: rename(
                    $srcFile, $path . '/src/Commands/' . ucfirst($pName) . 'Command.php'
                );
                !is_file($srcFile = $path . '/src/Middleware/Middleware.php') ?: rename(
                    $srcFile, $path . '/src/Middleware/' . ucfirst($pName) . 'Middleware.php'
                );

                //
                foreach ([
                    $path . '/src/Migrations/2017_08_11_171401_create_some_table.php' =>
                    $path . '/src/Migrations/2017_08_11_171401_create_' . $pName . '_table.php',

                    $path . '/database/migrations/2021_11_22_000001_create_some_table.php' =>
                    $path . '/database/migrations/2021_11_22_000001_create_' . $pName . '_table.php'
                ] as $srcFile => $outFile) {
                    !is_file($srcFile) ?: rename($srcFile, $outFile);
                }
                //
                !is_file($srcFile = $path . '/src/Contracts/Contract.php') ?: rename(
                    $srcFile, $path . '/src/Contracts/' . ucfirst($pName) . 'Contract.php'
                );
                !is_file($srcFile = $path . '/src/Classes/Class.php') ?: rename(
                    $srcFile, $path . '/src/Classes/' . ucfirst($pName) . 'Class.php'
                );

                $this->info('Yey! The package structure is ready for action!');
                $this->comment('Hey we are almost done with this! Let me add the class loader to the current composer project...');

                $lComposer = json_decode(file_get_contents(base_path('composer.json')), true);
                $lComposer['autoload']['psr-4'][$package->namespace . '\\'] = str_replace(base_path() . '/', '', $path) . '/src';
                file_put_contents(
                    base_path('composer.json'),
                    json_encode($lComposer, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                );

                $this->info('Your main composer.json file has been updated.');
                $this->comment('Seems like the only thing left is to dump the composer autoload...');
                if ($this->confirm('Do you want to dump composer auto-load?', 'yes')) {
                    if ($this->confirm('Do you have composer command in your path?', 'yes')) {
                        print shell_exec('cd ' . base_path() . ' && composer dump-autoload');
                    } else {
                        if ($this->confirm('Do you want me to try using the downloaded binary?', 'yes')) {
                            $cPath = base_path('vendor/composer/composer/bin/composer');
                            print shell_exec('cd ' . base_path() . ' && ' . $cPath . ' dump-autoload');
                        } else {
                            $cPath = $this->ask('Please specify the full composer executable file path');
                            print shell_exec('cd ' . base_path() . ' && ' . $cPath . ' dump-autoload');
                        }
                    }
                    $this->table(['namespace', 'path'], [[
                        'namespace' => $package->namespace,
                        'path' => str_replace(base_path() . '/', '', $path)
                    ]]);
                } else {
                    $this->line('Skipping the composer dump autoload...');
                    $this->line('Pleae manually dump the autoload: composer dump-autoload');
                }

                if ($this->confirm('Do you want to create README.md?', 'yes')) {
                    $this->comment('Creating README.md...');

                    $readme = fopen("$path/README.md", "w");
                    fwrite($readme, "# $package->name\n\n$package->description");
                    fclose($readme);

                    $this->info('Very cool! The README.md has been created!');
                } else {
                    $this->line('Skipping the creation of README.md...');
                }

                $this->comment('Searching for license File...');

                $licenses = scandir(__DIR__ . '/../Licenses');

                if (in_array($package->license, $licenses)) {
                    copy(__DIR__ . "/../Licenses/$package->license", "$path/LICENSE");
                    Helpers::strReplaceFile('{{ YEAR }}', date('Y'), "$path/LICENSE");
                    Helpers::strReplaceFile('{{ AUTHOR }}', $package->author, "$path/LICENSE");
                    $this->info('Nice! The LICENSE file is ready!');
                } else {
                    $this->error("Whoops! The License of your package is unknown for Bootpack so cannot be created automatically");
                }

                if ($this->confirm('Do you want to continue? Make sure the auto-load was dumped', 'yes')) {
                    $this->comment('Registering the service provider in the current laravel application...');

                    Helpers::strReplaceFile(
                        'App\\Providers\\RouteServiceProvider::class,',
                        "App\\Providers\\RouteServiceProvider::class,\n\t\t"
                        . $package->namespace . "\\" . ucfirst($pName) . 'ServiceProvider::class,',
                        base_path('config/app.php')
                    );

                    $this->info('Very cool! The service provider is registered!');

                    if ($this->confirm('Feeling like creating the git repository as well?', 'yes')) {
                        if ($this->confirm('Do you have git command in your path?', 'yes')) {
                            print shell_exec('cd ' . $path . ' && git init');
                        } else {
                            $gPath = $this->ask('Please specify the full git executable file path');
                            print shell_exec('cd ' . $path . ' && ' . $gPath . '  init');
                        }
                    }

                    $this->line('');
                    $this->logo('Please donate if you found this usefull');
                    $this->info('Congratulations! Your package is created, configured and ready to be coded :)');
                    $this->line('Package location: ' . $path);
                } else {
                    $this->line("Uhhh... Well... You'll need to dump the auto-load next time...");
                    $this->line("You'll also need to register the package service provider to the application before developing...");
                    $this->info('Aborted package extra steps...');
                    $this->line('Package location: ' . $path);
                }
            } else {
                $this->error("The folder '{$path}' already exists");
            }
        }
    }

    /**
     * @param string $path
     * @return string
     */
    protected function sourcePath($path = null)
    {
        switch (true) {
            case is_dir($path):
                return $path;
            case is_string($path) && is_dir($target = __DIR__ . '/../Sources/' . trim($path, '/\\')):
                return $target;
            default:
                return __DIR__ . '/../Sources/source';
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function package($name)
    {
        $package = new Package($name);

        return $package
            ->author($this->ask("What is your name?", $package->author))
            ->name($this->ask("What is the package name?", $package->name))
            ->description($this->ask("What is the package description?", $package->description))
            ->license($this->ask("What is the package license?", $package->license))
            ->usePhp($this->ask("What is the package min PHP version?", $package->php))
            ->useNamespace($this->ask("What is the package namespace?", $package->namespace));
    }

    /**
     * @param $msg
     */
    protected function logo($msg = '')
    {
        $this->line('');
        $this->line("  ____              _                    _    ");
        $this->line(" |  _ \            | |                  | |   ");
        $this->line(" | |_) | ___   ___ | |_ _ __   __ _  ___| | __");
        $this->line(" |  _ < / _ \ / _ \| __| '_ \ / _` |/ __| |/ /");
        $this->line(" | |_) | (_) | (_) | |_| |_) | (_| | (__|   <   " . $msg);
        $this->line(" |____/ \___/ \___/ \__| .__/ \__,_|\___|_|\_\\");
        $this->line("                       | |                    ");
        $this->line("                       |_|                    ");
        $this->line('');
    }
}
