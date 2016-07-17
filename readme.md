# IBanking Package for Laravel 5

This package allows you to crawl and parse your bank balance and statement. Currently available for Bank BCA and Bank Mandiri.
Inspired by the original [BCA Parser](http://www.randomlog.org/article/bca-parser-lagi/). Thank you yah gan =)

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
$ibank = IBanking::bank('bca');

$ibank->login();

$balance = $ibank->getBalance();

$statement = $ibank->getStatement(3); // mutation within last 3 days. Default: 1 (yesterday)

$ibank->logout();
```

The `logout()` method should be called to avoid single session at a time restriction from the internet banking provider.
This means if you don't call the `logout()` method at the end of your codes, you won't be able to login to your internet banking from anywhere until its session expired.

## Non-Laravel Usage

You can stil use IBanking without Laravel. This is how:

After running `composer require rick20/ibanking`, create a php file in your project folder and put the following codes

```php
require 'vendor/autoload.php';

use Rick20\IBanking\CrawlerParser;
use Rick20\IBanking\Providers\BCAProvider;

$ibank = new BCAProvider(new CrawlerParser(), 'username', 'password');
$ibank->login();
$balance = $ibank->getBalance();
$statement = $ibank->getStatement();
$ibank->logout();

// the rest of your code...
```

## Tips & Advice

You can place the above code under the Scheduled Command job (Laravel) and sets it to run [not more than 100x per day](http://www.randomlog.org/article/bca-parser-lagi/#comment-2912).
The less you run it per day, the less chances you are being suspended by the internet banking provider.
Please make any necessary effort to keep your ibank username and password safe and secure.
Changing your password regularly can help to keep it more secure.

## Bugs & Improvements

Feel free to report me any bug you found. I would be also very happy to receive pull requests for improvements and for other internet banking provider as well.