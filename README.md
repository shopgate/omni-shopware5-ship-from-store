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

You need a Shopware API user, which you have to set up in the Shopgate Administration, right where the Webhook is configured.

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

## Commands
Very helpful commands while coding:

- Import from Shopgate to Shopware: `bin/console sgate:impex:start sgate_inventory_import`
- Export from Shopware to Shopgate: `bin/console sgate:impex:start sgate_order_export`
- Export from Shopware to Shopgate: `bin/console sgate:impex:start sgate_customer_email_export`

Running with different php versions: `/opt/homebrew/opt/php@7.4/bin/php bin/console sgate:impex:start sgate_order_export`
