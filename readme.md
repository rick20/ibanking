# IBanking Package for Laravel 5

This package allows you to crawl and parse your bank balance and statement. Currently only available for Bank BCA and Bank Mandiri.
Inspired by the original [BCA Parser](www.randomlog.org/article/bca-parser-lagi/)

## Installation

To get started with IBanking, run this command or add the package to your `composer.json`

    composer require rick20/ibanking

## Configuration

After installing the IBanking package, register the `Rick20\IBanking\IBankingServiceProvider` in your `config/app.php` file.
Also, add the `IBanking` facade to the `aliases` array in your `app` configuration file:
```php
'IBanking' => Rick20\IBanking\Facades\IBanking::class,
```

Finally add these lines to your `config/services.php` file:
```php
'bca' => [
    'username' => 'your-klikbca-username',
    'password' => 'your-klikbca-password'
],
'mandiri' => [
    'username' => 'your-mandiri-username',
    'password' => 'your-mandiri-password'
]
```

## How To Use

After all sets, use the IBanking as follows:
```php
$ibank = IBanking::driver('bca');
$ibank->login(); // Must be called before anything else
$balance = $ibank->getBalance();
$statement = $ibank->getStatement();
$ibank->logout(); // Must be called after you are done.
```
Please be noted that if you forgot to call the `logout()` method, you will not be able to login to your account for a certain minutes.

## Attention!

While this package might useful for you to do automatic check on your balance statement, I don't responsible for any fraud that might come later. So please make any necessary effort to keep it safe. Changing your password regularly might help to keep it more secure.