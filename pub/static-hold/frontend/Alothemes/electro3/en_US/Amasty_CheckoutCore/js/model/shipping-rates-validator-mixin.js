/*global define*/
define(
    [
        'jquery',
        'mage/utils/wrapper',
        'Magento_Checkout/js/model/shipping-service'
    ],
    function (
        $,
        wrapper,
        shippingService
    ) {
        'use strict';
        return function (target) {
            var mixin = {
                validateAddressDataState: true,

                /**
                 * @return {*}
                 */
                postcodeValidation: function (original) {
                    original();

                    return true;
                },

                /**
                 * Fix validation for billing address
                 *
                 * @param {Function} original
                 * @param {Object} element
                 * @param {Number} delay
                 */
                bindHandler: function (original, element, delay) {
                    if (element.component.indexOf('/group') !== -1
                        || (element.name.indexOf('billing') === -1 && element.dataScope.indexOf('billing') === -1)
                    ) {
                        return original(element, delay);
                    }

                    if (element.index === 'postcode') {
                        var self = this;

                        delay = typeof delay === 'undefined' ? 1000 : delay;

                        element.on('value', function () {
                            clearTimeout(self.validateZipCodeTimeout);
                            self.validateZipCodeTimeout = setTimeout(function () {
                                self.postcodeValidation(element);
                            }, delay);
                        });
                    }
                },

                /**
                 * Save Validation State
                 *
                 * @param {Function} original
                 * @param {Object} address
                 * @return {Boolean}
                 */
                validateAddressData: function (original, address) {
                    this.validateAddressDataState = original(address);

                    return this.validateAddressDataState;
                },

                /**
                 * Hide Loader with disabled fields (ex. Country) - compatibility with 2.4.7
                 *
                 * @param {Function} original
                 * @returns {void}
                 */
                validateFields: function (original) {
                    original();

                    if (!this.validateAddressDataState) {
                        shippingService.isLoading(false);
                    }
                }
            };

            wrapper._extend(target, mixin);
            return target;
        };
    }
);
