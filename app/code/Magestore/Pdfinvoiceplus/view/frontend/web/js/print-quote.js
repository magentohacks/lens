/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'uiComponent',
    'jquery',
    'ko'
], function (Component, $, ko) {
    'use strict';
    return Component.extend({
        
        displayButton:ko.observable(window.checkout.displayButton),
        
        options: {
            printUrl: window.checkout.printQuoteUrl
        },

        initialize: function () {
            var self = this;
            this._super();
        },

        printQuote: function () {
            var options = this.options;
            window.open(options.printUrl, '_blank');
        }
    });
});
