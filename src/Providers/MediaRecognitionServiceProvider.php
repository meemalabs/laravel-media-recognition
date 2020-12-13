<?php

namespace Meema\MediaRecognition\Providers;

use Illuminate\Support\ServiceProvider;
use Meema\MediaRecognition\Facades\Recognize;
use Meema\MediaRecognition\MediaRecognitionManager;

class MediaRecognitionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/config.php' => config_path('media-recognition.php'),
            ], 'config');
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
