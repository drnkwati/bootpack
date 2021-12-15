<?php
namespace Drnkwati\Bootpack\Classes;

use Illuminate\Support\Collection;

class Package
{
    /**
     * @var mixed
     */
    public $name;
    /**
     * @var mixed
     */
    public $description;
    /**
     * @var mixed
     */
    public $author;
    /**
     * @var mixed
     */
    public $license;
    /**
     * @var mixed
     */
    public $php;
    /**
     * @var mixed
     */
    public $namespace;

    /**
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->author = config('bootpack.default_author');
        $this->license = config('bootpack.default_license');
        $this->description = config('bootpack.default_description');
        $this->php = config('bootpack.default_php');
        $ename = explode('/', $name);
        $this->namespace = ucfirst($ename[0]) . '\\' . ucfirst($ename[1]);
    }

    public function json()
    {
        return Collection::make([
            'name' => $this->name,
            'description' => $this->description,
            'license' => $this->license,
            'type' => 'library',
            'authors' => [[
                'name' => explode('<', $this->author)[0],
                'email' => Helpers::getBetween('<', '>', $this->author)
            ]],
            'require' => [
                'php' => ">={$this->php}",
                'illuminate/support' => '>=5.0'
            ],
            'require-dev' => [
                'orchestra/testbench' => '>=3.0'
            ],
            'autoload' => [
                'psr-4' => [
                    $this->namespace . '\\' => 'src/'
                ]
            ],
            'autoload-dev' => [
                'psr-4' => [
                    "Tests\\" => 'tests/'
                ]
            ],
            'extra' => [
                'laravel' => [
                    'providers' => [
                        $this->namespace . '\\' . ucfirst(explode('/', $this->name)[1]) . 'ServiceProvider'
                    ],
                    'aliases' => []
                ]
            ],
            'minimum-stability' => "dev"
        ])->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function name($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param $description
     * @return mixed
     */
    public function description($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param $author
     * @return mixed
     */
    public function author($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @param $license
     * @return mixed
     */
    public function license($license)
    {
        $this->license = $license;

        return $this;
    }

    /**
     * @param $php
     * @return mixed
     */
    public function usePhp($php)
    {
        $this->php = $php;

        return $this;
    }

    /**
     * @param $namespace
     * @return mixed
     */
    public function useNamespace($namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }
}
