/**
 * compatibility with 'braintree_paypal' payment method
 * when 'Terms and Conditions Checkbox Positioning' = 'Below the Order Total'
 * paymentMethodRenderer.item is empty
 * but 'braintree_paypal' require specific CheckboxId
 * @see Paypal_Braintree/js/view/payment/method-renderer/paypal.js:onInit()
 */
define([
], function () {
    'use strict';

    return function (Component) {
        return Component.extend({
            getCheckboxId: function (context, agreementId) {
                let paymentMethodRenderer = context.$parents[1];

                if (paymentMethodRenderer && !paymentMethodRenderer.item) {
                    return 'agreement_' + 'braintree_paypal' + '_' + agreementId;
                }

                return this._super(context, agreementId);
            }
        })
    }
});
