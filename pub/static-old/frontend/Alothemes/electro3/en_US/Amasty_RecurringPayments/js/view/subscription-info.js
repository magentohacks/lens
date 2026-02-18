define([
    'uiComponent',
    'Magento_Checkout/js/model/quote'
], function (Component, quote) {
    'use strict';

    const amastyRecurringConfig = window?.checkoutConfig?.amastyRecurringConfig ?? {};

    return Component.extend({
        defaults: {
            template: 'Amasty_RecurringPayments/checkout/subscription-info',
            isEnabled: false
        },

        /**
         * @returns {Object}
         */
        initObservable: function () {
            return this._super().observe(['isEnabled']);
        },

        /**
         * @returns {void}
         */
        initialize: function () {
            this._super();

            quote.paymentMethod.subscribe(({method}) => {
                this.isEnabled(this.hasRecurringProducts() && this.isPaypalExpress(method))
            });
        },

        /**
         * @param {string} method
         * @returns {Boolean}
         */
        isPaypalExpress: function (method) {
            return method === 'paypal_express' || method === 'amasty_recurring_paypal';
        },

        /**
         * @returns {Boolean}
         */
        hasRecurringProducts: function () {
            return !!amastyRecurringConfig?.isRecurringProducts;
        }
    });
});
