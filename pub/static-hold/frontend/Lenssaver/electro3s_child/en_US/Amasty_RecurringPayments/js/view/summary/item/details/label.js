define([
    'jquery',
    'uiComponent',
    'mage/tooltip'
], function ($, Component) {
    'use strict';

    return Component.extend({
        tooltip: {
            contentSelector: '[data-amrec-js="tooltip-content"]',
            contentClassName: 'amrec-tooltip-content',
            contentText: $.mage.__('You can not edit the Subscription product details on the Checkout page. Please, get back to the Shopping Cart to make changes')
        },

        /**
         * Checks visibility.
         *
         * @return {Boolean}
         */
        isVisible: function (itemId) {
            return this.recurring_items[itemId];
        },

        initTooltip: function (element) {
            $(element).tooltip({
                position: {
                    my: 'right bottom',
                    at: 'right top',
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
