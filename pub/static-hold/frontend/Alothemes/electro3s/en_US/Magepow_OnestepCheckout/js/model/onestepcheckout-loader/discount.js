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
        'Magento_Checkout/js/model/quote'
    ],
    function (ko, quote) {
        'use strict';
        var totals = quote.getTotals(),
        couponCode = ko.observable(null);
        if (totals()) {
            couponCode(totals()['coupon_code']);
        }
        return {
            isLoading: ko.observable(false),
            isAppliedCoupon: ko.observable(couponCode() != null),
            /**
             * Start full page loader action
             */
            startLoader: function () {
                this.isLoading(true);
            },
            /**
             * Stop full page loader action
             */
            stopLoader: function () {
                this.isLoading(false);
            }
        };
    }
);
