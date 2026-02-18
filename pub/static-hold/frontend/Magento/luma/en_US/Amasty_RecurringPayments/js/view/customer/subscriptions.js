/*browser:true*/
/*global define*/
define([
    'jquery',
    'underscore',
    'ko',
    'uiComponent',
    'Magento_Ui/js/block-loader',
    'mage/url'
], function ($, _, ko, Component, blockLoader, url) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Amasty_RecurringPayments/view/customer/subscriptions',
            isLoading: ko.observable(false),
            visible: ko.observable(false),
            subscriptions: ko.observable([]),
            cancelUrl: '',
            loaderUrl: '',
            nextBillingDateTooltipEnabled: false,
            nextBillingDateTooltipText: '',
            subscriptionLink: 'amasty_recurring/customer/subscription'
        },

        initialize: function () {
            this._super();

            if (this.loaderUrl) {
                blockLoader(this.loaderUrl);
            }

            this.visible(!!this.subscriptionsData.length);

            $.each(this.subscriptionsData, function (key, subscription) {
                subscription.detailsVisibility = ko.observable(false);
            });

            this.subscriptions(this.subscriptionsData);
        },

        toggleDetailsVisibility: function (subscription) {
            subscription.detailsVisibility(!subscription.detailsVisibility());
        },

        getSubscriptionLink: function (subscription) {
            return url.build(this.subscriptionLink) + `?subscription_id=${subscription.subscription_id}`
                + `&subscription_payment=${subscription.payment_method}`;
        },

        cancelClick: function (subscriptionInfo) {
            var confirmationPopup = $('[data-amrec-js="cancel-confirmation-popup"]');

            confirmationPopup.show();

            $('[data-amrec-js="close-confirmation"]').on('click', function () {
                confirmationPopup.hide();
                subscriptionInfo = null;
            });

            $('[data-amrec-js="cancel-subscription"]').on('click', function () {
                var postData;

                if (!subscriptionInfo) {
                    return;
                }

                postData = {
                    subscription_id: subscriptionInfo.subscription.subscription_id,
                    subscription_payment: subscriptionInfo.subscription.payment_method,
                };

                confirmationPopup.hide();
                this.isLoading(true);

                $.ajax({
                    type: 'POST',
                    url: this.cancelUrl,
                    data: postData,
                    dataType: 'json',
                    success: function (data) {
                        $.each(data, function (key, subscription) {
                            subscription.detailsVisibility = ko.observable(false);
                        });
                        this.subscriptions(data);
                        this.visible(data.length);
                    }.bind(this),
                    complete: function () {
                        this.isLoading(false);
                    }.bind(this)
                });
            }.bind(this));
        }
    });
});
