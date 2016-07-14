<?php

namespace Rick20\IBanking\Providers;

use Symfony\Component\DomCrawler\Crawler;

class BcaProvider extends AbstractProvider
{
    public function login()
    {
        $this->getHttpClient()->get('https://m.klikbca.com/login.jsp');

        $this->getHttpClient()->post('https://m.klikbca.com/authentication.do', [
            'headers' => [
                'Referer' => 'https://m.klikbca.com/login.jsp'
            ],
            'form_params' => $this->getLoginData()
        ]);
    }

    public function logout()
    {
        $this->getHttpClient()->get('https://m.klikbca.com/authentication.do?value(actions)=logout', [
            'headers' => [
                'Referer' => 'https://m.klikbca.com/authentication.do?value(actions)=menu'
            ]
        ]);
    }

    public function read()
    {
        $this->login();

        $this->getHttpClient()->get('https://m.klikbca.com/accountstmt.do?value(actions)=menu', [
            'headers' => [
                'Referer' => 'https://m.klikbca.com/authentication.do'
            ]
        ]);

        $response = $this->getHttpClient()->get('https://m.klikbca.com/balanceinquiry.do', [
            'headers' => [
                'Referer' => 'https://m.klikbca.com/accountstmt.do?value(actions)=menu'
            ]
        ]);

        $this->logout();

        $crawler = new Crawler($response->getBody()->getContents());

        return $crawler
            ->filter('#pagebody > span > table')
            ->last()
            ->filter('table > tbody > tr')
            ->last()
            ->filter('td')
            ->last()
            ->filter('b')
            ->text();
    }

    private function getLoginData()
    {
        $ip = $this->getIPAddress();

        return [
            'value(user_id)' => $this->username,
            'value(pswd)' => $this->password,
            'value(Submit)' => 'LOGIN',
            'value(actions)' => 'login',
            'value(user_ip)' => $ip,
            'user_ip' => $ip,
            'value(mobile)' => 'true',
            'mobile' => 'true',
        ];
    }

    private function getIPAddress()
    {
        return file_get_contents('https://api.ipify.org/');
    }
}
