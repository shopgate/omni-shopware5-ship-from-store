//{namespace name="backend/order/main"}
//{block name="backend/order/view/detail/overview" append}
Ext.define('Shopware.apps.SgateOrder.view.detail.Overview', {
    override: 'Shopware.apps.Order.view.detail.Overview',

    initComponent: function() {
        var me = this;

        me.snippets.details.sgateShipFromStoreOrderNumber = '{s name="overview/details/sgateShipFromStoreOrderNumber"}Shopgate Bestellung{/s}';
        me.callParent(arguments);

    },

    createRightDetailElements: function() {
        var me = this,
            elements = me.callParent(arguments);

        elements.splice(
            elements.length - 1,
            0,
            { name: 'sgateShipFromStoreLink', fieldLabel: me.snippets.details.sgateShipFromStoreOrderNumber, allowHtml: true, renderer: me.renderShopgateLink }
        );

        return elements;
    },

    renderShopgateLink: function(value) {
        if(value) {
            return Ext.String.format('<a target="_blank" href="[0]">'+'{s name="overview/details/sgateShipFromStoreLink"}Zu Shopgate{/s}'+'</a>', value);
        }

        return '{s name="overview/details/sgateShipFromStoreLinkNotAvailable"}Nicht verfügbar{/s}';
    }
});
//{/block}