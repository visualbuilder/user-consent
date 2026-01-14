# Testing Notes

## Test Status

### ✅ All Tests Passing (22/22)
- `Tests\ArchTest` - 1 test passing
- `Tests\ExampleTest` - 1 test passing
- `Tests\RequestTest` - 9 tests passing (validation tests for optional/mandatory consents and questions)
- `Tests\ResourcesTest` - 11 tests passing (includes question CRUD tests via relation manager)

## RequestTest Fixes (2026-01-14)

### Fixed: Flaky Test Issues

The RequestTest suite had intermittent failures caused by incomplete test data seeding:

**Issue 1: Missing `published_at` timestamps**
- Factory default `published_at => now()` was overridden when using `createMany()` with explicit attributes
- `ConsentOption::findbykeys()` filters with `whereDate('published_at', '<=', now())`
- Null timestamps caused queries to return no results

**Issue 2: Random `is_survey` values**
- Factory used `$faker->boolean()` for `is_survey` field
- `outstandingConsents()` filters with `where('is_survey', false)`
- Randomly generated `true` values caused consent options to be excluded

**Solution:**
Added explicit values to `tests/Seeders/ConsentOptionSeeder.php`:
- `'published_at' => now()`
- `'is_survey' => false`

All 4 RequestTest tests now pass consistently (verified with 10 consecutive runs).

## ResourcesTest Fixes (2026-01-14)

### Fixed: Livewire 3 Validation Compatibility Issue

The ResourcesTest suite (6 tests) was failing with a Livewire 3 validation error:

```
Illuminate\Support\ViewErrorBag::put(): Argument #2 ($bag) must be of type
Illuminate\Contracts\Support\MessageBag, null given
```

**Root Cause:**
Livewire 3's `SupportValidation` hook was calling `$this->component->getErrorBag()` which returned `null` during HTTP request tests in the Orchestra Testbench environment.

**Solution:**
Implemented Livewire hook overrides to handle null error bags gracefully:

1. **Created override classes** in `tests/Livewire/Features/`:
   - `SupportValidation.php` - Overrides Livewire's validation hook to ensure error bags are never null
   - `SupportTesting.php` - Overrides Livewire's testing hook to handle null error bags
   - Files must use Livewire's exact namespace (`Livewire\Features\SupportValidation` and `Livewire\Features\SupportTesting`)

2. **Configured composer autoload** in `composer.json`:
   - Added `tests/Livewire/Features/` to the `autoload-dev` classmap
   - This ensures override classes are loaded before vendor classes during tests
   - After adding, run `composer dump-autoload` to regenerate autoload files

3. **Streamlined test environment** in `tests/TestCase.php`:
   - Removed unnecessary service providers (ActionsServiceProvider, InfolistsServiceProvider, etc.)
   - Improved migration loading using `loadMigrationsFrom(__DIR__ . '/migrations')`
   - Created symlinks in `tests/migrations/` pointing to stub files
   - Added automatic test data seeding in `setUp()` method

**Result:**
All 11 ResourcesTest tests now pass consistently:
- ✅ it can access user consent list page
- ✅ it can list user consents
- ✅ it can access user consent create page
- ✅ it can create user consent
- ✅ it can access user consent edit page
- ✅ it can update user consent
- ✅ it can load the questions relation manager in consents
- ✅ it can list questions in consents
- ✅ it can create question in consents
- ✅ it can update question in consents
- ✅ it can delete question in consents

**How the Overrides Work:**

The override classes in `tests/Livewire/Features/` replace Livewire's internal hooks by using Livewire's exact namespace (`Livewire\Features\SupportValidation` and `Livewire\Features\SupportTesting`). When composer's classmap loads these files before Livewire's vendor PSR-4 autoloading, our patched versions take precedence.

The key fix in both override classes:
```php
$errorBag = $this->component->getErrorBag();

if ($errorBag === null || !($errorBag instanceof \Illuminate\Contracts\Support\MessageBag)) {
    $errorBag = new MessageBag();
    $this->component->setErrorBag($errorBag);
}
```

This ensures error bags are always valid MessageBag instances, preventing the type error.

**Important Notes:**
- These overrides only load during tests (via `autoload-dev`)
- Production code is unaffected
- The directory structure matches the namespace (`tests/Livewire/Features/` → `Livewire\Features\...`), reducing IDE warnings
- Composer's classmap loading takes precedence over PSR-4, allowing our classes to override vendor classes
- The classmap path `tests/Livewire/Features/` is intentionally kept broad to automatically include future override classes

## Summary

All 22 tests in the test suite are now passing. The test environment has been properly configured to work with Filament 3 and Livewire 3 without compatibility issues.

The test suite now includes:
- Comprehensive consent option CRUD tests
- Question CRUD tests through the relation manager (emphasizing that questions are attached to consents)
- Validation tests for optional vs mandatory consents
- Validation tests for optional vs required questions
- Request validation tests ensuring proper handling of missing fields

### Test Suite Performance
- Total tests: 22 passed
- All tests are stable and non-flaky

### Maintenance
If tests fail again after updating Livewire, check if the override classes need updating to match new Livewire hook signatures.
