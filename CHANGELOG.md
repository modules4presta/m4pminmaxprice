# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project
adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-09-27

### Added

- Minimum and maximum price per product and per combination, edited on the product page.
- Price clamping through `actionProductPriceCalculation`, applied after every other price rule.
- Tax handling: limits are entered tax excluded and converted for tax included prices.
- English and Polish translations, MIT license and the standard documentation set.

### Fixed

- The limits table was never shown, because `displayAdminProductsExtra` was not registered.
- Submitted limits were read from the hook parameters instead of the request, so nothing was saved.
- The insert helper was called with its arguments in the wrong order.
