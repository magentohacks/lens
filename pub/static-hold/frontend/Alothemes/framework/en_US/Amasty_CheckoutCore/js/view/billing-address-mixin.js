/**
 * Billing address view mixin for store flag is billing form in edit mode (visible)
 */
define([
    'underscore',
    'Magento_Checkout/js/model/quote',
    'Amasty_CheckoutCore/js/model/address-form-state'
], function (_, quote, formService) {
    'use strict';

    return function (billingAddress) {
        /**
         * force initialization for correct isAddressSameAsShipping working
         * @see Magento_Checkout/js/view/billing-address
         */
        var updateDependenciesBounced = _.debounce(function () {
            quote.billingAddress.valueHasMutated();
        }, 50);

        return billingAddress.extend({
            initialize: function () {
                this._super();

                /**
                 * Vendor Module Fix
                 * Magento EE 2.4.2
                 * Module: Vertex_AddressValidation
                 * File: view/frontend/web/js/billing-validation-mixin.js; method addressDetailsVisibilityChanged
                 * Issue: observable isAddressDetailsVisible could be changed by other components
                 * before registry.get() is executed => this.addressValidator is null =>
                 * => this.addressValidator.message is browser console error
                 */
                if (!this.addressValidator) {
                    this.addressValidator = {
                        message: {
                            hasMessage: function () {
                                return false;
                            }
                        }
                    }
                }

                return this;
            },

            initObservable: function () {
                this._super();

                if (quote.billingAddress() && this.isAddressSameAsShipping.getVersion() <= 1) {
                    updateDependenciesBounced();
                }

                this.isAddressSameAsShipping.subscribe(formService.updateBillingFormStates, formService);
                this.isAddressDetailsVisible.subscribe(formService.updateBillingFormStates, formService);

                if (window.checkoutConfig.displayBillingOnPaymentMethod) {
                    quote.paymentMethod.subscribe(formService.updateBillingFormStates, formService);
                }

                formService.updateBillingFormStates();
                formService.isFormRendered(true);

                return this;
            }
        });
    };
});
