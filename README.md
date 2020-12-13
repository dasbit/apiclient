# apiclient

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Simple api client wrapper without throwing exceptions with statuscodes

## Install

Via Composer

``` bash
$ composer require dasbit/apiclient
```

## Usage

``` php
$api = new dasbit\apiclient(new Guzzle, 'https://example-host.com/api');
$api->authenticate('login', 'password');
$response = $api->request('/entity-list');
$code = $response['code'];
$responseBody = $response['body']; // assoc array of decoded json string
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email abylhasov@gmail.com instead of using the issue tracker.

## Credits

- [Dastan Abylkhassov][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/dasbit/apiclient.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/dasbit/apiclient/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/dasbit/apiclient.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/dasbit/apiclient.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/dasbit/apiclient.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/dasbit/apiclient
[link-travis]: https://travis-ci.org/dasbit/apiclient
[link-scrutinizer]: https://scrutinizer-ci.com/g/dasbit/apiclient/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/dasbit/apiclient
[link-downloads]: https://packagist.org/packages/dasbit/apiclient
[link-author]: https://github.com/dasbit
[link-contributors]: ../../contributors
