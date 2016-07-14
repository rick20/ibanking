<?php

namespace Rick20\IBanking\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Rick20\IBanking\IBankingManager
 */
class IBanking extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Rick20\IBanking\Contracts\Factory';
    }
}
