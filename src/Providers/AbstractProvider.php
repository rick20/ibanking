<?php

namespace Rick20\IBanking\Providers;

use GuzzleHttp\Client;
use Rick20\IBanking\Contracts\Parser;

abstract class AbstractProvider
{
    /**
     * The Document Parser instance.
     *
     * @var \Rick20\IBanking\Contracts\Parser
     */
    protected $parser;

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
     * Tells the $httpClient wheter to use POST method in every request
     *
     * @var string
     */
    protected $alwaysPOST = false;

    /**
     * Create a new provider instance.
     *
     * @param  string  $username
     * @param  string  $password
     * @return void
     */
    public function __construct(Parser $parser, $username, $password)
    {
        $this->parser = $parser;
        $this->username = $username;
        $this->password = $password;
    }

    abstract protected function getLoginPageUrl();

    abstract protected function getAuthFormUrl();

    abstract protected function getAuthFormData();

    abstract protected function getLogoutUrl();

    abstract protected function getBalancePageUrl();

    abstract protected function getBalanceXPath();

    abstract protected function getStatementFormUrl();

    abstract protected function getStatementFormData($daysBackward);

    abstract protected function getStatementXPath();

    abstract protected function buildStatementItem(Parser $row);


    protected function initCookies()
    {
        $this->visit($this->getLoginPageUrl());
    }

    public function login()
    {
        $this->initCookies();

        $this->submit($this->getAuthFormUrl(), $this->getAuthFormData());
    }

    public function logout()
    {
        $this->visit($this->getLogoutUrl());
    }

    public function getBalance()
    {
        $parsedBalance = $this
            ->visit($this->getBalancePageUrl())
            ->parse($this->getBalanceXPath());

        if (! $parsedBalance->found()) {
            return false;
        }

        return $this->normalizeAmount($parsedBalance->text());
    }

    public function getStatement($daysBackward = 1)
    {
        $parsedStatement = $this
            ->submit(
                $this->getStatementFormUrl(),
                $this->getStatementFormData($daysBackward)
            )
            ->parse($this->getStatementXPath());

        if (! $parsedStatement->found()) {
            return false;
        }

        return array_filter($parsedStatement->each(
            function (Parser $row, $i) {
                if ($i == 0) return false; // table header
                try {
                    return $this->buildStatementItem($row);
                } catch (\InvalidArgumentException $err) {
                    return false;
                }
            }
        ));
    }

    protected function getHttpClient()
    {
        if (! $this->httpClient) {
            $this->httpClient = new Client([
                'cookies' => true,
                'headers' => [
                    'User-Agent' => $this->useragent
                ]
            ]);
        }

        return $this->httpClient;
    }

    public function setHttpClient(Client $client)
    {
        $this->httpClient = $client;

        return $this;
    }

    protected function visit($url)
    {
        if ($this->alwaysPOST) {
            return $this->submit($url);
        }

        $response = $this->getHttpClient()->get($url, [
            'allow_redirects' => [
                'referer' => true,
                'protocols' => ['https'],
                'track_redirects' => true,
            ]
        ]);

        return $this->parser->make($response->getBody()->getContents());
    }

    protected function submit($url, $data = [])
    {
        $response = $this->getHttpClient()->post($url, [
            'form_params' => $data,
            'allow_redirects' => [
                'referer' => true,
                'protocols' => ['https'],
                'track_redirects' => true,
            ]
        ]);

        return $this->parser->make($response->getBody()->getContents());
    }

    protected function normalizeAmount($amount)
    {
        if ($this->usesCommaForCents($amount = trim($amount))) {
            $amount = str_replace(',', '.', str_replace('.', '', $amount));
        }

        return floatval(preg_replace("/[^-0-9\.]/", '', $amount));
    }

    private function usesCommaForCents($amount)
    {
        return substr($amount, -3, 1) === ',';
    }

}
