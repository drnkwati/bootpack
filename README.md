<p align="center">
    <img src="http://i.imgur.com/viDkXrW.png">
    <h1 align="center">Bootpack - Laravel 5 package bootstraper</h1>
</p>

## Description

Bootpack is a laravel 5 package bootstraper that does the following:

-   Creates composer.json for a laravel package
-   Create a basic well structured package directory
-   Adds the local autoloader to the project composer.json
-   Dumps the autoload
-   Adds the package service provider to the laravel project
-   Initiates a git repository
-   Perhaps more...

It features a full terminal application based on an artisan command.

## Installation

```
composer require drnkwati/bootpack
```

Register the service provider to the current project (Not needed if using laravel 5.5+):

```
Drnkwati\Bootpack\BootpackServiceProvider::class
```

Publish the configuration:

```
php artisan vendor:publish
```

## Usage

Can't be more simple... rename test/package to the vendor/packagename notation you wish to create.

Example: test/package

```
php artisan bootpack:create test/package
```
If you desire a minimal setup, pass in the option --source=legacy or provide a custom template path.

```
php artisan bootpack:create test/package --source=legacy
```
```
php artisan bootpack:create test/package --source=~/path/to/custom/package/template/directory
```

You then have a cool functional terminal to help you create the package. Enjoy!

![First](public/img/Screen-Shot.png?raw=true "Bootpack Screen Shot")
