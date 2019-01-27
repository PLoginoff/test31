<?php

namespace PL\Providers;

class DataProvider implements ProviderInterface
{
    protected $host;
    protected $user;
    protected $password;

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     */
    public function __construct(string $host, string $user, string $password)
    {
        $this->host     = $host;
        $this->user     = $user;
        $this->password = $password;
    }

    /**
     * @param array $request
     * @return array
     */
    public function get(array $request) : array
    {
        return [mt_rand(), mt_rand(),]; // returns a response from external service
    }
}
