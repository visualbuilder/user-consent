<?php

namespace Visualbuilder\FilamentUserConsent;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Route;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Visualbuilder\FilamentUserConsent\Commands\FilamentUserConsentCommand;
use Visualbuilder\FilamentUserConsent\Testing\TestsFilamentUserConsent;

class FilamentUserConsentServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-user-consent';

    public static string $viewNamespace = 'user-consent';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasRoute('web')
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('visualbuilder/user-consent');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }

        $this->registerRoutes();
    }

    public function packageRegistered(): void
    {
    }
    protected function registerRoutes()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/user-consent/{$file->getFilename()}"),
                ], 'user-consent-stubs');
            }
            $this->publishResources();
        }

        // Testing
        Testable::mixin(new TestsFilamentUserConsent());
    }

    protected function getAssetPackageName(): ?string
    {
        return 'visualbuilder/user-consent';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('user-consent', __DIR__ . '/../resources/dist/components/user-consent.js'),
            //            Css::make('user-consent-styles', __DIR__ . '/../resources/dist/user-consent.css'),
            //            Js::make('user-consent-scripts', __DIR__ . '/../resources/dist/user-consent.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            FilamentUserConsentCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_consent_options_table', 
            'create_user_consent_questions_table',
            'create_user_consent_question_options_table',
            'create_product_consent_option_table', 
            'create_product_category_consent_option_table', 
            'create_organisation_consent_option_table',
            'create_consentables_table', 
            'create_consentable_responses_table'
        ];
    }

    protected function publishResources()
    {
        $this->publishes([
                             __DIR__.'/../resources/views' => resource_path('views/vendor/user-consent'),
                         ], 'filament-user-consent-assets');
    }
}
