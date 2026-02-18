define([
    'jquery',
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote',
    'ko',
    'Amasty_RecurringPayments/js/model/update-estimation',
    'underscore',
    'mage/tooltip'
], function ($, abstractTotal, quote, ko, updateEstimation, _) {
    'use strict';

    return abstractTotal.extend({
        defaults: {
            displayArea: 'after_details'
        },
        tooltip: {
            contentSelector: '[data-amrec-js="tooltip-content"]',
            contentClassName: 'amrec-tooltip-content',
            contentText: $.mage.__('Next billing amount is calculated based on the subscription price of the product and estimated shipping cost and is charged as soon as your subscription starts.')
        },

        /**
         * Initializes observable properties.
         *
         * @returns {Component} Chainable.
         */
        initObservable: function () {
            this._super();

            if (window.checkoutConfig.amastyRecurringConfig.isRecurringProducts) {
                updateEstimation.subscribeToCartChangesOnce()();
            }

            return this;
        },

        getValue: function (quoteItem) {
            return ko.computed(function () {
                var currentItem = _.find(quote.totals().items, function (item) {
                    return item.item_id === quoteItem.item_id && this.isVisible(item);
                }, this);

                if (currentItem) {
                    return this.getFormattedPrice(currentItem.extension_attributes.amasty_recurrent_estimate);
                }

                return '';
            }, this);
        },

        /**
         * @param {Object} quoteItem
         * @returns {boolean}
         */
        isVisible: function (quoteItem) {
            return Object.prototype.hasOwnProperty.call(quoteItem, 'extension_attributes')
                && Object.prototype.hasOwnProperty.call(
                    quoteItem.extension_attributes,
                    'amasty_recurrent_estimate'
                );
        },

        initTooltip: function (element) {
            $(element).tooltip({
                position: {
                    my: 'right top',
                    at: 'right bottom',
                    collision: 'flipfit flip',
                },
                items: this.tooltip.contentSelector,
                tooltipClass: this.tooltip.contentClassName,
                content: function () {
                    return this.tooltip.contentText;
                }.bind(this)
            });
        }
    });
});
