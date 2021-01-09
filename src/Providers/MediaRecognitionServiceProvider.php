<?php

namespace Meema\MediaRecognition\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Meema\MediaRecognition\Facades\Recognize;
use Meema\MediaRecognition\Http\Middleware\VerifySignature;
use Meema\MediaRecognition\MediaRecognitionManager;

class MediaRecognitionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/config.php' => config_path('media-recognition.php'),
            ], 'config');
        }

        if (! class_exists('CreateMediaRecognitionsTable')) {
            $this->publishes([
                __DIR__.'/../../database/migrations/create_media_recognitions_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_media_recognitions_table.php'),
            ], 'migrations');
        }

        $this->loadRoutesFrom(__DIR__.'/../routes.php');

        $router = $this->app->make(Router::class);

        if (! in_array('verify-signature', $router->getMiddleware())) {
            $router->aliasMiddleware('verify-signature', VerifySignature::class);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'media-recognition');

        $this->registerMediaRecognitionManager();

        $this->registerAliases();
    }

    /**
     * Registers the Text to speech manager.
     *
     * @return void
     */
    protected function registerMediaRecognitionManager()
    {
        $this->app->singleton('recognize', function ($app) {
            return new MediaRecognitionManager($app);
        });
    }

    /**
     * Register aliases.
     *
     * @return void
     */
    protected function registerAliases()
    {
        $this->app->alias(Recognize::class, 'Recognize');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            'recognize',
        ];
    }
}
