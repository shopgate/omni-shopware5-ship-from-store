# Shopgate Ship-from-Store

This plugin connects Shopware with Shopgate and

 - imports cumulated inventories from Shopgate to Shopware
 - exports orders from Shopware to Shopgate
 - updates order status from Shopgate to Shopware

## Installation

This plugin can be installed via the Shopware plugin manager. Just upload the zipped plugin or download it from the Shopware Store.
Click "Install" and "Activate" to activate the plugin.

Make sure Shopware cronjobs are beeing processed regularly otherwise orders won't be exported to Shopgate and cumulated inventories won't be imported. See how to setup cronjobs for Shopware: [System: Cronjobs](https://docs.shopware.com/de/shopware-5-de/einstellungen/system-cronjobs#cronjob-einrichten)

For order status updates you have to subscribe to the Shopgate "salesOrderStatusUpdated" event. You can find the complete webhook url in the plugin configuration in Shopware backend.

## Configuration
Take care of the plugin configuration. Otherwise some processes will end up in misbehavior.

**Product code:** Choose wether your products should be identified by article number (SKU) or EAN
**Update stock updates of last X minutes:** Only inventory updates from the last X minutes will be imported. Default is 15
**Error mail addresses:** Leave a comma-separated list of mail addresses to which errors will be sent
**Payment status:** Optional - if filled only orders with this payment status will be exported to Shopgate
**Environment:** Choose wether your Shogate environment is in production or staging

**Production / Staging**
Leave your API credentials here. There is one section for production environment and one for staging. Choose your credentials with the configuration field *Environment*.
You will get your API credentials from Shopgate.

**Order state mapping**
Map each Shopgate order state with a Shopware order state. This mapping will be used for order status updates.

