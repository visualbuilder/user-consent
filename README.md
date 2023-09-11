# Provides User Consent options for Filament applications

[![Latest Version on Packagist](https://img.shields.io/packagist/v/visualbuilder/filament-user-consent.svg?style=flat-square)](https://packagist.org/packages/visualbuilder/filament-user-consent)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/visualbuilder/filament-user-consent/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/visualbuilder/filament-user-consent/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/visualbuilder/filament-user-consent/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/visualbuilder/filament-user-consent/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/visualbuilder/filament-user-consent.svg?style=flat-square)](https://packagist.org/packages/visualbuilder/filament-user-consent)


- Create and edit consent options
- Apply them to user models
- Include a consent form during registration
- Email a copy of the consents to the user
- Users already accepted can be asked to accept the updated consent
- Provide users with My Consents page allowing review of their given consents
- Provide admin panel users with a list of consents provided by all users


## Installation

You can install the package via composer:

```bash
composer require visualbuilder/filament-user-consent
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-user-consent-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-user-consent-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-user-consent-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$filamentUserConsent = new Visualbuilder\FilamentUserConsent();
echo $filamentUserConsent->echoPhrase('Hello, Visualbuilder!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Lee Evans](https://github.com/cannycookie)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
