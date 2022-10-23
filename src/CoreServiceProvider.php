<?php

namespace Novacio;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CoreServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name("abbieben/laravel-kit")
            ->hasConfigFile("github")
            ->publishesServiceProvider("MacroServiceProvider")
            ->hasViews("")
            ->hasAssets();
    }
}
