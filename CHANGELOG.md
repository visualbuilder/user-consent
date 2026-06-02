# Changelog

All notable changes to `user-consent` will be documented in this file.

## 5.0.6 - 2026-06-03

- Reaching the consent page with nothing left to consent to (e.g. pressing back after submitting) no longer shows a `403 No required consent` error — it now follows the same redirect as a successful submission, letting the host app's normal flow decide where the user goes next

## 5.0.5 - 2026-06-02

- Fixed the submit button jumping size while saving: replaced the two-button (idle / "Submitting consents…") markup with a single button that uses Filament's built-in in-place loading spinner
- The button label is now singular ("Submit Consent") when only one consent is outstanding

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
