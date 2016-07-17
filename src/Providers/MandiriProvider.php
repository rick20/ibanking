<?php

namespace Rick20\IBanking\Providers;

use Carbon\Carbon;
use Rick20\IBanking\Contracts\Parser;
use Symfony\Component\DomCrawler\Crawler;

class MandiriProvider extends AbstractProvider
{
    protected $accountid;

    protected function getLoginPageUrl()
    {
        return 'https://ib.bankmandiri.co.id/retail/Login.do?action=form&lang=in_ID';
    }

    protected function getAuthFormUrl()
    {
        return 'https://ib.bankmandiri.co.id/retail/Login.do';
    }

    protected function getAuthFormData()
    {
        return [
            'userID' => $this->username,
            'password' => $this->password,
            'action' => 'result',
            'image.x' => '0',
            'image.y' => '0'
        ];
    }

    protected function getLogoutUrl()
    {
        return 'https://ib.bankmandiri.co.id/retail/Logout.do?action=result';
    }

    protected function getBalancePageUrl()
    {
        if (! $this->accountid) {
            $this->obtainAccountID();
        }

        return 'https://ib.bankmandiri.co.id/retail/AccountDetail.do?action=result&ACCOUNTID=' . $this->accountid;
    }

    protected function getBalanceXPath()
    {
        return '//form/table/tr[4]/td[2]/div/table[2]/tr[5]';
    }

    protected function getStatementFormUrl()
    {
        return 'https://ib.bankmandiri.co.id/retail/TrxHistoryInq.do';
    }

    protected function getStatementFormData($daysBackward)
    {
        if (! $this->accountid) {
            $this->obtainAccountID();
        }

        $end = Carbon::now();
        $start = Carbon::now()->subDays($daysBackward);

        return [
            'action' => 'result',
            'fromAccountID' => $this->accountid,
            'searchType' => 'R',
            'sortType' => 'Date',
            'orderBy' => 'ASC',
            'fromDay' => $start->format('d'),
            'fromMonth' => $start->format('m'),
            'fromYear' => $start->format('Y'),
            'toDay' => $end->format('d'),
            'toMonth' => $end->format('m'),
            'toYear' => $end->format('Y')
        ];
    }

    protected function getStatementXPath()
    {
        return "//form/table/tr[4]/td[2]/div/table[3]/tr";
    }

    protected function buildStatementItem(Parser $row)
    {
        $dbamount = trim($row->parse("//td[3]")->text());
        $cramount = trim($row->parse("//td[4]")->text());

        return [
            'date' => $row->parse("//td[1]")->text(),
            'desc' => str_replace("<br>", " | ", $row->parse("//td[2]")->html()),
            'type' => ($dbamount == '0,00') ? 'CR' : 'DB',
            'amount' => $this->normalizeAmount(($dbamount == '0,00') ? $cramount : $dbamount)
        ];
    }

    private function obtainAccountID()
    {
        return $this->accountid = $this
            ->submit('https://ib.bankmandiri.co.id/retail/TrxHistoryInq.do?action=form')
            ->parse("//*[@name='fromAccountID']/option[2]")
            ->attr('value');
    }
}
