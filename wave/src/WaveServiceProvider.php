<?php

declare(strict_types=1);

namespace Wave;

use Exception;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Vite as BaseVite;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Intervention\Image\ImageManagerStatic;
use Laravel\Folio\Folio;
use Livewire\Livewire;
use Wave\Facades\Wave as WaveFacade;
use Wave\Http\Middleware\InstallMiddleware;
use Wave\Http\Middleware\Subscribed;
use Wave\Http\Middleware\ThemeDemoMiddleware;
use Wave\Http\Middleware\TokenMiddleware;
use Wave\Http\Middleware\VerifyPaddleWebhookSignature;
use Wave\Overrides\Vite;
use Wave\Plugins\PluginServiceProvider;

class WaveServiceProvider extends ServiceProvider
{
    public function register(): void
    {

        $loader = AliasLoader::getInstance();
        $loader->alias('Wave', WaveFacade::class);

        $this->app->singleton('wave', fn () => new Wave());

        $this->loadHelpers();

        $this->loadLivewireComponents();

        $this->app->router->aliasMiddleware('paddle-webhook-signature', VerifyPaddleWebhookSignature::class);
        $this->app->router->aliasMiddleware('subscribed', Subscribed::class);
        $this->app->router->aliasMiddleware('token_api', TokenMiddleware::class);

        if (! $this->hasDBConnection()) {
            $this->app->router->pushMiddlewareToGroup('web', InstallMiddleware::class);
        }

        if (config('wave.demo')) {
            $this->app->router->pushMiddlewareToGroup('web', ThemeDemoMiddleware::class);
            // Overwrite the Vite asset helper so we can use the demo folder as opposed to the build folder
            $this->app->singleton(BaseVite::class, fn ($app)
                // Replace the default Vite instance with the custom one
                => new Vite());
        }

        // Register the PluginServiceProvider
        $this->app->register(PluginServiceProvider::class);
    }

    public function boot(Router $router, Dispatcher $event): void
    {

        Relation::morphMap([
            'users' => config('wave.user_model'),
        ]);

        $this->registerFilamentComponentsFriendlyNames();

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'wave');
        $this->loadMigrationsFrom(realpath(__DIR__.'/../database/migrations'));
        $this->loadBladeDirectives();
        $this->setDefaultThemeColors();

        FilamentColor::register([
            'danger' => Color::Red,
            'gray' => Color::Zinc,
            'info' => Color::Blue,
            'primary' => config('wave.primary_color'),
            'success' => Color::Green,
            'warning' => Color::Amber,
        ]);

        Validator::extend('imageable', function ($attribute, $value, $params, $validator): bool {
            try {
                ImageManagerStatic::make($value);

                return true;
            } catch (Exception) {
                return false;
            }
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Wave\Console\Commands\CancelExpiredSubscriptions::class,
                \Wave\Console\Commands\CreatePluginCommand::class,
            ]);
            // $this->excludeInactiveThemes();
        }

        Relation::morphMap([
            'user' => config('auth.providers.model'),
            'form' => \App\Models\Forms::class,
            // Add other mappings as needed
        ]);

        $this->registerWaveFolioDirectory();
        $this->registerWaveComponentDirectory();
    }

    protected function loadHelpers()
    {
        foreach (glob(__DIR__.'/Helpers/*.php') as $filename) {
            require_once $filename;
        }
    }

    protected function loadMiddleware()
    {
        foreach (glob(__DIR__.'/Http/Middleware/*.php') as $filename) {
            require_once $filename;
        }
    }

    protected function loadBladeDirectives()
    {

        // app()->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {
        // @admin directives
        Blade::if('admin', fn () => ! auth()->guest() && auth()->user()->isAdmin());

        // @subscriber directives
        Blade::if('subscriber', fn () => ! auth()->guest() && auth()->user()->subscriber());

        // @notsubscriber directives
        Blade::if('notsubscriber', fn () => ! auth()->guest() && ! auth()->user()->subscriber());

        // Subscribed Directives
        Blade::if('subscribed', fn ($plan) => ! auth()->guest() && auth()->user()->subscribedToPlan($plan));

        // home directives
        Blade::if('home', fn () => request()->is('/'));

    }

    protected function registerFilamentComponentsFriendlyNames()
    {
        // Blade::component('filament::components.avatar', 'avatar');
        Blade::component('filament::components.dropdown.index', 'dropdown');
        Blade::component('filament::components.dropdown.list.index', 'dropdown.list');
        Blade::component('filament::components.dropdown.list.item', 'dropdown.list.item');
    }

    protected function registerWaveFolioDirectory()
    {
        if (File::exists(base_path('wave/resources/views/pages'))) {
            Folio::path(base_path('wave/resources/views/pages'))->middleware([
                '*' => [

                ],
            ]);
        }
    }

    protected function registerWaveComponentDirectory()
    {
        Blade::anonymousComponentPath(base_path('wave/resources/views/components'));
    }

    protected function setDefaultThemeColors()
    {
        if (config('wave.demo')) {
            $theme = $this->getActiveTheme();

            if (isset($theme->id)) {
                if (Cookie::get('theme')) {
                    $theme_cookied = \DevDojo\Themes\Models\Theme::query()->where('folder', '=', Cookie::get('theme'))->first();

                    if (isset($theme_cookied->id)) {
                        $theme = $theme_cookied;
                    }
                }

                $default_theme_color = match ($theme->folder) {
                    'anchor' => '#000000',
                    'blank' => '#090909',
                    'cove' => '#0069ff',
                    'drift' => '#000000',
                    'fusion' => '#0069ff'
                };

                Config::set('wave.primary_color', $default_theme_color);
            }
        }
    }

    protected function getActiveTheme()
    {
        return \Wave\Theme::query()->where('active', 1)->first();
    }

    protected function hasDBConnection()
    {
        $hasDatabaseConnection = true;

        try {
            DB::connection()->getPdo();
        } catch (Exception) {
            $hasDatabaseConnection = false;
        }

        return $hasDatabaseConnection;
    }

    private function loadLivewireComponents(): void
    {
        Livewire::component('billing.checkout', \Wave\Http\Livewire\Billing\Checkout::class);
        Livewire::component('billing.update', \Wave\Http\Livewire\Billing\Update::class);
    }
}
