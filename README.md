# Monero Daemon & Wallet RPC
[![Latest Stable Version](http://poser.pugx.org/refactor_ring/monero-rpc-php/v)](https://packagist.org/packages/refactor_ring/monero-rpc-php)
[![Tests](https://github.com/refactor-ring/monero-rpc-php/actions/workflows/tests.yml/badge.svg)](https://github.com/refactor-ring/monero-rpc-php/actions/workflows/tests.yml)
[![PHPStan](https://github.com/refactor-ring/monero-rpc-php/actions/workflows/phpstan.yml/badge.svg)](https://github.com/refactor-ring/monero-rpc-php/actions/workflows/phpstan.yml)
[![codecov](https://codecov.io/gh/refactor-ring/monero-rpc-php/graph/badge.svg?token=P8K26M8W6N)](https://codecov.io/gh/refactor-ring/monero-rpc-php)

Monero daemon and wallet RPC client library written in modern PHP.

**WARNING:** The API might still undergo significant changes so use at own risk

## Features
* Implements Monero wallet and daemon json-rpc methods
* Support authentication for the wallet and daemon rpc servers
* Completely strong-typed
* PSR-18 compatible, so different http client libraries can be used

<a name="installation"></a>
## Installation

You can install the package with Composer:

```bash
composer require refactor_ring/monero-rpc-php
```

## Usage

First make sure to have http client library like guzzlehttp/guzzle or symfony/http-client installed.

For the wallet rpc client:
```php
$client = (new Builder('http://127.0.0.1:18081/json_rpc'))
    ->buildWalletClient();

echo $client->getVersion()->version;
```
Daemon rpc client:
```php
$client = (new Builder('http://127.0.0.1:18081/json_rpc'))
    ->buildDaemonClient();

echo $client->getVersion()->version;
```

With authentication:
```php
$client = (new Builder('http://127.0.0.1:18081/json_rpc'))
    ->withAuthentication('foo', 'bar')
    ->buildDaemonClient();

echo $client->getVersion()->version;
```

## Testing

The project has unit tests and integration tests, the unit tests can be run using `composer test:unit`

To run the integration tests, you'll need `docker` and `docker compose`  or you could run `monerod` and `monero-wallet-rpc` on your own.

If you have the docker stack installed, go to the `tests` folder and run `docker compose up`. Note that the daemon will run on port `18081` and `monero-wallet-rpc` will run on port `18083`.

After that, run `composer test:integration`

Also, you can run `docker compose down` to stop and remove the containers started by `docker compose up`.

## Roadmap
- [ ] More wallet-rpc integration tests
- [ ] Fix strong-typed model classes for certain responses:
  - [ ] BlockHash in some places
  - [ ] Introduce a Address class maybe
- [ ] More thorough error handling
- [ ] Decide on giving the different get_blockheader* methods the same response

##  Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md)


##  Changelog

See [CHANGELOG.md](CHANGELOG.md)

<a name="license"></a>
## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Acknowledgments
* [monero-rpc-rs](https://github.com/monero-rs/monero-rpc-rs) - The project's extensive testsuite was used for setting up this project's testuite
* [monero-php](https://github.com/monero-integrations/monerophp) - Thanks for providing the php ecosystem with a Monero library during all these years!
* [Monero](https://getmonero.org) - Thanks to everybody involved!