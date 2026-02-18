define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Hemage_CheckoutNewsletterSubscription/js/model/newsletter-validator'
    ],
    function (Component, additionalValidators, newsletterValidator) {
        'use strict';

        additionalValidators.registerValidator(newsletterValidator);

        return Component.extend({});
    }
);