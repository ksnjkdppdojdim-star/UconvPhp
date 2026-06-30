# UConv PHP

Lightweight PHP unit converter (PSR-4).

## Installation

**Via Composer (recommandé)**

```
composer require uconv/uconv
```

**Ou depuis le dépôt (pour développement)**

```
composer install
```

## Usage

```php
<?php
require 'vendor/autoload.php';

use Uconv\Uconv;

echo Uconv::convert("10km", "m");      // 10000
echo Uconv::convert("5lbs", "kg");     // 2.26796
echo Uconv::convert("1hr", "min");     // 60
echo Uconv::convert("100USD", "EUR");  // ~85
```

## Text counter

```php
use Uconv\Counter;

Counter::increment("server.request");      // 1
Counter::increment("server.request");      // 2
Counter::increment("loop.iteration");      // 1

echo Counter::get("server.request");       // 2
print_r(Counter::all());                   // ["server.request" => 2, "loop.iteration" => 1]
```

The counter only counts text and returns numbers. You can use the returned value
with a file, a JSON document, a database, logs, or any other storage you choose.
By default, counts are kept in memory for the current PHP process.

`Uconv::convert()` also increments the internal `uconv.requests` counter:

```php
Uconv::resetRequestCount();
Uconv::convert("10km", "m");

echo Uconv::getRequestCount();             // 1
```

## Testing

```
./vendor/bin/phpunit tests/
```

## Features

- PSR-4 namespaces
- Static API `Uconv::convert()`
- Custom exceptions
- Exact Node.js/Python parity
