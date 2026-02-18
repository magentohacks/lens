/**
 * By default Magento flow, when payment method selected billing address is updates.
 * And when billing address updates, isPlaceOrderActionAllowed also update.
 * But One Step Checkout optimize billing address KO update. @see onepage.replaceEqualityComparer
 * So we need update isPlaceOrderActionAllowed on payment method change, to emulate default flow.
 * Also we added placeOrderState for Place Order button. Thus, we can flexibly manage its state.
 */
define([
    'jquery',
    'ko',
    'underscore',
    'Magento_Checkout/js/model/quote',
    'Amasty_CheckoutCore/js/model/payment/place-order-state',
    'Amasty_CheckoutCore/js/model/payment/payment-loading'
], function ($, ko, _, quote, placeOrderState, paymentLoader) {
    'use strict';

    return function (Component) {
        return Component.extend({
            initObservable: function () {
                this._super();

                this.isPlaceOrderActionAllowed = ko.pureComputed({
                    read: this.isPlaceOrderActionAllowedRead,
                    write: this.isPlaceOrderActionAllowedWrite,
                    owner: this
                });

                return this;
            },

            /**
             * Read function for KO computed isPlaceOrderActionAllowed.
             *
             * Don't call this function directly.
             * Use isPlaceOrderActionAllowed().
             *
             * @returns {boolean}
             */
            isPlaceOrderActionAllowedRead: function () {
                return quote.billingAddress() !== null && placeOrderState() && !paymentLoader();
            },

            /**
             * Write function for KO computed isPlaceOrderActionAllowed.
             *
             * Don't call this function directly.
             * Use isPlaceOrderActionAllowed(value).
             *
             * @param {boolean} value
             * @returns {boolean}
             */
            isPlaceOrderActionAllowedWrite: function (value) {
                return placeOrderState(!!value);
            }
        });
    };
});
