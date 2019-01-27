<?php

namespace PL\Providers;

/**
 * Interface ProviderInterface
 *
 * @author  Pavel <ploginoff@gmail.com>
 * @package PL\Providers
 */
interface ProviderInterface
{
    /**
     * Get data via provider
     *
     * @throws \Exception
     *
     * @param  array $request
     * @return array
     */
    public function get(array $request) : array;
}
