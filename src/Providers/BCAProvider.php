<?php

namespace Rick20\IBanking\Providers;

use Carbon\Carbon;
use Rick20\IBanking\Contracts\Parser;
use Symfony\Component\DomCrawler\Crawler;

class BCAProvider extends AbstractProvider
{
    protected $alwaysPOST = true;

    protected function getLoginPageUrl()
    {
        return 'https://m.klikbca.com/login.jsp';
    }

    protected function getAuthFormUrl()
    {
        return 'https://m.klikbca.com/authentication.do';
    }

    protected function getAuthFormData()
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

    protected function getLogoutUrl()
    {
        return 'https://m.klikbca.com/authentication.do?value(actions)=logout';
    }

    protected function getBalancePageUrl()
    {
        return 'https://m.klikbca.com/balanceinquiry.do';
    }

    protected function getBalanceXPath()
    {
        return "//*[@id='pagebody']/span/table[2]/tr/td[2]/table/tr[2]/td[3]";
    }

    protected function getStatementFormUrl()
    {
        return 'https://m.klikbca.com/accountstmt.do?value(actions)=acctstmtview';
    }

    protected function getStatementFormData($daysBackward)
    {
        $end = Carbon::now();
        $start = Carbon::now()->subDays($daysBackward);

        return [
            'r1' => 1,
            'value(D1)' => 0,
            'value(startDt)' => $start->format('d'),
            'value(startMt)' => $start->format('m'),
            'value(startYr)' => $start->format('Y'),
            'value(endDt)' => $end->format('d'),
            'value(endMt)' => $end->format('m'),
            'value(endYr)' => $end->format('Y')
        ];
    }

    protected function getStatementXPath()
    {
        return "//*[@id='pagebody']/span/table[2]/tr[2]/td[2]/table/tr";
    }

    protected function buildStatementItem(Parser $row)
    {
        $arrDescs = explode('<br>', $row->parse("//td[2]")->html());

        return [
            'date' => $row->parse("//td[1]")->text(),
            'desc' => implode(" | ", $arrDescs),
            'type' => $row->parse("//td[3]")->text(),
            'amount' => $this->normalizeAmount(end($arrDescs))
        ];
    }

    private function getIPAddress()
    {
        return file_get_contents('https://api.ipify.org/');
    }
}
