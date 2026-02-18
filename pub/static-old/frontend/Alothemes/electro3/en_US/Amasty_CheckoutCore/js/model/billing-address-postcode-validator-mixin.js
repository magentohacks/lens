define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    return function (target) {
        const mixin = {

            /**
             * for Correct Validation of the Billing Address Form
             *
             * @param {Function} original
             * @param {Object} postcodeElement
             * @returns {*}
             */
            postcodeValidation: function (original, postcodeElement) {
                let shippingAddressCountryNode = $('.form-shipping-address select[name="country_id"]'),
                    validationResult;

                shippingAddressCountryNode.attr('name', 'amcountry_id');
                validationResult = original(postcodeElement);
                shippingAddressCountryNode.attr('name', 'country_id');

                return validationResult;
            }
        }

        wrapper._extend(target, mixin);

        return target;
    };
});
