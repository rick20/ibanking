<?php

namespace Rick20\IBanking\Contracts;

interface Factory
{
    /**
     * Get an bank provider implementation.
     *
     * @param  string  $driver
     * @return \Rick20\IBanking\Contracts\Provider
     */
    public function driver($driver = null);
}
