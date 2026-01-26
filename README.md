# Provides User Consent options for Filament applications

[![Latest Version on Packagist](https://img.shields.io/packagist/v/visualbuilder/user-consent.svg?style=flat-square)](https://packagist.org/packages/visualbuilder/user-consent)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/visualbuilder/user-consent/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/visualbuilder/user-consent/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/visualbuilder/user-consent/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/visualbuilder/user-consent/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/visualbuilder/user-consent.svg?style=flat-square)](https://packagist.org/packages/visualbuilder/user-consent)


- Create and edit consent options
- Apply them to user models
- Include a consent form during registration
- Email a copy of the consents to the user
- Users already accepted can be asked to accept the updated consent
- Provide users with My Consents page allowing review of their given consents
- Provide admin panel users with a list of consents provided by all users


## Version Compatibility

| Package Version | Filament | Laravel | PHP |
|-----------------|----------|---------|-----|
| 5.x | 5.x | 11.x, 12.x | 8.2+ |
| 4.x | 4.x | 11.x | 8.2+ |
| 3.x | 3.x | 10.x, 11.x | 8.1+ |

## Installation

You can install the package via composer:

```bash
# For Filament 5.x
composer require visualbuilder/user-consent:^5.0

# For Filament 4.x
composer require visualbuilder/user-consent:^4.0
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="user-consent-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="user-consent-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="user-consent-views"
```

## Usage

### 1. Register the Plugin

Add the plugin to your Filament panel provider:

```php
use Visualbuilder\FilamentUserConsent\FilamentUserConsentPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamentUserConsentPlugin::make(),
        ]);
}
```

### 2. Add the Trait to Your User Model

```php
use Visualbuilder\FilamentUserConsent\Traits\HasConsents;

class User extends Authenticatable
{
    use HasConsents;
}
```

### 3. Create Consent Options

Use the admin panel to create consent options (e.g., Terms of Service, Privacy Policy, Marketing Communications).

### 4. Display Consent Form

The package provides a Livewire component for displaying consent forms:

```php
// In your registration or profile page
<livewire:consent-option-form-builder />
```

### 5. Check User Consents

```php
// Get outstanding consents for a user
$outstandingConsents = $user->outstandingConsents();

// Check if user has accepted a specific consent
$hasAccepted = $user->hasAcceptedConsent('terms-of-service');
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
