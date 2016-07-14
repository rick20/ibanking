<?php

namespace Rick20\IBanking;

use Rick20\IBanking\Contracts\Parser;
use Illuminate\Support\ServiceProvider;

class IBankingServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Rick20\IBanking\Contracts\Parser', function ($app) {
            return new CrawlerParser();
        });

        $this->app->singleton('Rick20\IBanking\Contracts\Factory', function ($app) {
            return new IBankingManager($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['Rick20\IBanking\Contracts\Factory'];
    }
}
