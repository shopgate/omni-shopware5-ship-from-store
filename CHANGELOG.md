
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
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



    <changelog version="1.3.0">
        <changes lang="de">
            - Performance des Lagerbestandsimports verbessert
        </changes>
        <changes>
            - Optimized performance of inventory import
        </changes>
    </changelog>

    <changelog version="1.4.0">
        <changes lang="de">
            - Versendete Menge der Bestellpositionen wird mit Shopgate abgeglichen
            - Zeitzone gefixt
        </changes>
        <changes>
            - Shipped amount of order line items will be synced with Shopgate
            - Fixed timezone
        </changes>
    </changelog>

    <changelog version="1.4.1">
        <changes lang="de">
            - Versand-Menge der Positionen wird nur noch gesetzt, wenn die Bestellung nicht storniert ist
        </changes>
        <changes>
            - Only set shipped amount of line items of order is not canceled
        </changes>
    </changelog>

    <changelog version="1.4.2">
        <changes lang="de">
            - Versand-Menge der Positionen wird nur noch gesetzt, wenn Bestellung und Position nicht storniert sind
        </changes>
        <changes>
            - Only set shipped amount of line items of order and line item are not canceled
        </changes>
    </changelog>

    <changelog version="1.4.3">
        <changes lang="de">
            - Versand-Menge der Positionen wird nur noch gesetzt, wenn Position sich im Status "fulfilled" befindet
        </changes>
        <changes>
            - Only set shipped amount of line items if line item is in state "fulfilled"
        </changes>
    </changelog>

    <changelog version="1.4.4">
        <changes lang="de">
            - Der Name der Bezahlart wird an die Kundennotiz angehangen.
        </changes>
        <changes>
            - Payment name will be added to customer note.
        </changes>
    </changelog>

    <changelog version="1.4.5">
        <changes lang="de">
            - Die Felder "unitPromoAmount" und "promoAmount" an den einzelnen Bestellpositionen werden jetzt mit exportiert und fließen in die Berechnung der Gesamtsummen.
        </changes>
    </changelog>

[Unreleased]: https://github.com/shopgate/cart-integration-magento2-export/compare/2.9.31...HEAD
[1.4.4]: https://github.com/shopgate/omni-shopware5-ship-from-store/compare/44320df4...b6110598
[1.2.0]: https://github.com/shopgate/omni-shopware5-ship-from-store/compare/a4571767...44320df4
