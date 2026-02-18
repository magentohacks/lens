/**
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
define(
    [
        'ko',
        'jquery',
        'Magento_Checkout/js/view/payment',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magepow_OnestepCheckout/js/model/checkout-data-resolver',
        'Magepow_OnestepCheckout/js/model/payment-service',
        'mage/translate'
    ],
    function (ko,
              $,
              Component,
              quote,
              stepNavigator,
              additionalValidators,
              DataResolver,
              PaymentService) {
        'use strict';

        DataResolver.resolveDefaultPaymentMethod();

        return Component.extend({
            defaults: {
                template: 'Magepow_OnestepCheckout/payment'
            },
            isLoading: PaymentService.isLoading,
            errorValidationMessage: ko.observable(false),

            initialize: function () {
                var self = this;

                this._super();

                stepNavigator.steps.removeAll();

                additionalValidators.registerValidator(this);

                quote.paymentMethod.subscribe(function () {
                    self.errorValidationMessage(false);
                });

                return this;
            },

            validate: function () {
                if (!quote.paymentMethod()) {
                    this.errorValidationMessage($.mage.__('Please specify a payment method.'));

                    return false;
                }

                return true;
            }
        });
    }
);
