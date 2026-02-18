/**
 * Override store pickup quote mixin to set equalityComparer for shipping address before it override with computed
 */
define([
    'ko',
    'Magento_InventoryInStorePickupFrontend/js/model/pickup-address-converter',
    './quote-mixin'
], function (ko, pickupAddressConverter, quoteMixin) {
    'use strict';

    return function (quote) {
        // amasty fix start
        quote = quoteMixin(quote);
        // amasty fix finish
        var shippingAddress = quote.shippingAddress;

        /**
         * Makes sure that shipping address gets appropriate type when it points
         * to a store pickup location.
         */
        quote.shippingAddress = ko.pureComputed({
            /**
             * Return quote shipping address
             */
            read: function () {
                return shippingAddress();
            },

            /**
             * Set quote shipping address
             */
            write: function (address) {
                shippingAddress(
                    pickupAddressConverter.formatAddressToPickupAddress(address)
                );
            }
        });

        return quote;
    };
});
