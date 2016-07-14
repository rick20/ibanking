<?php

namespace Rick20\IBanking;

use Illuminate\Support\Manager;

class IBankingManager extends Manager
{
    protected function createBcaDriver()
    {
        $config = $this->app['config']['services.bca'];

        return $this->buildProvider(
            'Rick20\IBanking\Providers\BcaProvider', $config
        );
    }

    protected function createMandiriDriver()
    {
        $config = $this->app['config']['services.mandiri'];

        return $this->buildProvider(
            'Rick20\IBanking\Providers\MandiriProvider', $config
        );
    }

    public function buildProvider($provider, $config)
    {
        return new $provider($config['username'], $config['password']);
    }

    /**
     * Get the default driver name.
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No Internet Banking driver was specified.');
    }
}
