<?php

namespace InertiaKit\InertiaTableBuilder;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use InertiaKit\InertiaTableBuilder\Commands\InertiaTableBuilderCommand;

class InertiaTableBuilderServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('inertia-table-builder')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_inertia_table_builder_table')
            ->hasCommand(InertiaTableBuilderCommand::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../resources/js' => resource_path('js/vendor/inertia-table-builder'),
        ], 'inertia-table-builder-js');
    }
}
