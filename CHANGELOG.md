
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
## [1.4.8] - 2024-10-31
### Fixed
- line items of an order not exported to Shopgate when their associated product has no main image
- line items of an order not exported to Shopgate when they don't have a product associated anymore

## [1.4.7] - 2024-10-22
### Fixed
- error importing orders to Shopgate in some cases when changing line items

## [1.4.6] - 2024-05-28
### Added
- ZIP archives that can be uploaded and installed using the Shopware 5 plugin manager

## [1.4.5] - 2024-04-23
### Added
- exporting "unitPromoAmount" and "promoAmount" on order line items

### Fixed
- order total calculation now includes promotion amounts from order line items

## [1.4.4] - 2024-01-04
### Added
- payment method name to customer note on an order

### Changed
- shipped amount of line items is now only set when the line item is in status "fulfilled"

### Fixed
- using wrong time zone

## [1.2.0] - 2023-10-17
### Added
- exporting sale prices in the order export

### Fixed
- order positions now updated on order update
- missing customers' shop IDs

## 1.0.0 - 2023-07-26
Initial release.

[Unreleased]: https://github.com/shopgate/omni-shopware5-ship-from-store/compare/1.4.8...HEAD
[1.4.8]: https://github.com/shopgate/omni-shopware5-ship-from-store/compare/1.4.7...1.4.8
[1.4.7]: https://github.com/shopgate/omni-shopware5-ship-from-store/compare/1.4.6...1.4.7
[1.4.6]: https://github.com/shopgate/omni-shopware5-ship-from-store/compare/1.4.5...1.4.6
[1.4.5]: https://github.com/shopgate/omni-shopware5-ship-from-store/compare/b6110598...1.4.5
[1.4.4]: https://github.com/shopgate/omni-shopware5-ship-from-store/compare/44320df4...b6110598
[1.2.0]: https://github.com/shopgate/omni-shopware5-ship-from-store/compare/a4571767...44320df4
