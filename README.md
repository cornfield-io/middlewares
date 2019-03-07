# cornfield-io/middlewares

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

[PSR-15](https://www.php-fig.org/psr/psr-15/): HTTP Server Request Handlers implementation.

## Install

Via Composer

``` bash
$ composer require cornfield-io/middlewares
```

### Requirements

* You need `PHP >= 7.2.0` to use `Cornfield\Middlewares` but the latest stable version of PHP is recommended.

* An implementation of PSR-7 (we recommend the [Zend Diactoros project](https://github.com/zendframework/zend-diactoros/)).

Throughout this documentation, we will assume that you are using the packages above. If you want, you can still install other implementations of [PSR-7](https://packagist.org/providers/psr/http-message-implementation).

## Usage

All middlewares added must implement `Psr\Http\Server\MiddlewareInterface`.

```php
<?php

use Cornfield\Middlewares\Middlewares;
use Zend\Diactoros\ServerRequestFactory;

$middlewares = new Middlewares();
$request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);

// Prepend one or more MiddlewareInterface
$middlewares->unshift(...);

// Push one or more MiddlewareInterface
$middlewares->push(...);

// Process the request
$response = $middlewares->handle($request);
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Development

``` bash
$ composer dev
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email instead of using the issue tracker.

## Credits

- [cornfield-io][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/cornfield-io/middlewares.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/cornfield-io/middlewares/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/cornfield-io/middlewares.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/cornfield-io/middlewares.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/cornfield-io/middlewares.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/cornfield-io/middlewares
[link-travis]: https://travis-ci.org/cornfield-io/middlewares
[link-scrutinizer]: https://scrutinizer-ci.com/g/cornfield-io/middlewares/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/cornfield-io/middlewares
[link-downloads]: https://packagist.org/packages/cornfield-io/middlewares
[link-author]: https://github.com/cornfield-io
[link-contributors]: ../../contributors
