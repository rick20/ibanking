<?php

namespace Rick20\IBanking\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

abstract class AbstractProvider
{
    /**
     * The HTTP Client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * The account username.
     *
     * @var string
     */
    protected $username;

    /**
     * The account password.
     *
     * @var string
     */
    protected $password;

    /**
     * The user agent simulated.
     *
     * @var string
     */
    protected $useragent = 'Mozilla/5.0 (Linux; U; Android 2.3.7; en-us; Nexus One Build/GRK39F) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1';

    /**
     * Create a new provider instance.
     *
     * @param  string  $username
     * @param  string  $password
     * @return void
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Perform login for the provider.
     *
     * @return string
     */
    abstract public function login();

    /**
     * Get a instance of the Guzzle HTTP client.
     *
     * @return \GuzzleHttp\Client
     */
    protected function getHttpClient()
    {
        if (is_null($this->httpClient)) {
            $this->httpClient = new Client([
                'cookies' => true,
                'headers' => [
                    'User-Agent' => $this->useragent
                ]
            ]);
        }

        return $this->httpClient;
    }

    /**
     * Set the Guzzle HTTP client instance.
     *
     * @param  \GuzzleHttp\Client  $client
     * @return $this
     */
    public function setHttpClient(Client $client)
    {
        $this->httpClient = $client;

        return $this;
    }
}
