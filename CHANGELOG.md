# Changelog

All notable changes to `user-consent` will be documented in this file.

## 5.0.4 - 2026-06-02

- Made the `consent-option-request` page component configurable via `config('filament-user-consent.pages.consent_option_form_builder')`, so host apps can register their own subclass without redefining the route
- Defaults to the package's `ConsentOptionFormBuilder` (no behaviour change for existing installs)

## 5.0.0 - 2026-03-31

- Added Filament 5.x compatibility
- Updated to Livewire 4
- Updated test dependencies (Pest 4, PHPUnit 12)
- All 22 tests passing with zero code changes required

## 1.0.0 - 202X-XX-XX

- initial release
