define([
    'jquery',
    'uiComponent',
    'Magento_Ui/js/modal/confirm',
    'mage/translate'
], function ($, Component, confirm, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            subscriptionId: '',
            subscriptionPayment: '',
            cancelUrl: '',
            pauseUrl: '',
            resumeUrl: '',
            deliveryUrl: '',
            shippingUrl: '',
            popupUrl: '',
            selectors: {
                pauseBtn: '.action.-pause',
                cancelBtn: '.action.-cancel',
                resumeBtn: '.action.-resume',
                shippingBtn: '.-profile-shipping .-edit',
                deliveryBtn: '.-profile-delivery .-edit',
                popupContainer: '.amrec-popup.amrec-subscription-popup',
                popupClose: '.amrec-popup-confirmation .amrec-close',
                popupFocusStart: '.amrec-popup-confirmation .focus-trap-start',
                popupFocusEnd: '.amrec-popup-confirmation .focus-trap-end',
                popup: '[data-amrec-js="amrec-confirmation-popup"]',
                popupHeader: '.amrec-popup-confirmation .amrec-header',
                popupText: '.amrec-popup-confirmation .amrec-text',
                popupCancelBtn: '.amrec-popup-confirmation .amrec-button.-cancel',
                popupConfirmBtn: '.amrec-popup-confirmation .amrec-button.-confirm'
            },
            popupOptions: {
                cancel: {
                    headerText: $t('Are you sure you want to cancel this subscription?'),
                    popupText: $t('In case you cancel it by mistake, you will have to place a new order '
                        + 'to purchase the same subscription again, since it is currently not possible '
                        + 'to automatically renew any subscription after cancellation.'),
                    cancelText: $t('No, keep subscription active'),
                    confirmText: $t('Yes, cancel this subscription')
                },
                pause: {
                    headerText: $t('Are you sure you want to proceed?'),
                    popupText: $t('You are about to pause your current subscription. While your subscription is on pause, '
                        + 'you will not receive any recurring deliveries or charges.'),
                    cancelText: $t('Cancel'),
                    confirmText: $t('Ok')
                },
                shipping: {
                    headerText: $t('Are you sure you want to proceed?'),
                    popupText: $t('If you change your shipping address, taxes and totals may be recalculated. '
                        + 'Are you sure you want to proceed?'),
                    cancelText: $t('Cancel'),
                    confirmText: $t('Ok')
                },
                delivery: {
                    headerText: $t('It may be necessary to recreate your subscription'),
                    popupText: $t('If the subscription changes affect the payment amount or delivery plan, '
                        + 'the current subscription will need to be canceled and a new one created to apply the updates.'),
                    cancelText: $t('Keep Current'),
                    confirmText: $t('Continue')
                }
            },
            keycodes: {
                esc: 27,
                enter: 13
            }
        },

        /**
         * @returns {void}
         */
        initialize: function () {
            this._super();
            this.initHandlers();
            this.initPopupHandlers();
            this.initWcagHandlers();
        },

        /**
         * @returns {void}
         */
        initHandlers: function () {
            $(this.selectors.cancelBtn).on('click', () => {
               this.cancelClick()
            });

            $(this.selectors.pauseBtn).on('click', () => {
                this.pauseClick()
            });

            $(this.selectors.resumeBtn).on('click', () => {
                this.resumeClick()
            });

            $(this.selectors.shippingBtn).on('click', () => {
                this.shippingClick()
            });

            $(this.selectors.deliveryBtn).on('click', () => {
                this.deliveryClick()
            });
        },

        /**
         * @returns {void}
         */
        initWcagHandlers: function () {
            $(this.selectors.popupFocusStart).on('focus', () => {
                $(this.selectors.popupContainer + ' :focusable:last').focus();
            });

            $(this.selectors.popupFocusEnd).on('focus', () => {
                $(this.selectors.popupContainer + ' :focusable:first').focus();
            });

            $(this.selectors.popup).on('keydown', (e) => {
                if (e.which === this.keycodes.esc) {
                    $(this.selectors.popup).hide();
                }
            });

            $(this.selectors.popupClose).on('keydown', (e) => {
                if (e.which === this.keycodes.enter) {
                    $(this.selectors.popup).hide();
                }
            });
        },

        initPopupHandlers: function () {
            $('[data-amrec-js="close-confirmation"]').on('click', () => {
                $(this.selectors.popup).hide();
            });
        },

        /**
         * @param {Object} options
         * @returns {void}
         */
        openPopup: function ({headerText, popupText, cancelText, confirmText, confirmUrl}) {
            const confirmationPopup = $(this.selectors.popup);

            this.popupUrl = confirmUrl;
            $(this.selectors.popupHeader).text(headerText);
            $(this.selectors.popupText).text(popupText);
            $(this.selectors.popupCancelBtn).text(cancelText);
            $(this.selectors.popupConfirmBtn).text(confirmText);

            $(this.selectors.popupConfirmBtn).off('click');
            $(this.selectors.popupConfirmBtn).on('click', () => {
                window.location.href = this.popupUrl;
            });

            confirmationPopup.show();

            $(this.selectors.popupClose).focus();
        },

        /**
         * @returns {void}
         */
        cancelClick: function () {
            if (!this.subscriptionId || !this.subscriptionPayment) {
                return;
            }

            const confirmUrl = this.cancelUrl + `?subscription_id=${this.subscriptionId}`
                + `&subscription_payment=${this.subscriptionPayment}`;

            this.openPopup({ ...this.popupOptions.cancel, confirmUrl: confirmUrl});
        },

        /**
         * @returns {void}
         */
        pauseClick: function () {
            const confirmUrl = this.pauseUrl + `?subscription_id=${this.subscriptionId}`
                + `&subscription_payment=${this.subscriptionPayment}`;

            this.openPopup({ ...this.popupOptions.pause, confirmUrl: confirmUrl});
        },

        /**
         * @returns {void}
         */
        resumeClick: function () {
            window.location.href = this.resumeUrl + `?subscription_id=${this.subscriptionId}`
                + `&subscription_payment=${this.subscriptionPayment}`;
        },

        /**
         * @returns {void}
         */
        shippingClick: function () {
            const confirmUrl = this.shippingUrl + `?subscription_id=${this.subscriptionId}`
                + `&subscription_payment=${this.subscriptionPayment}`;
            if (this.subscriptionPayment === "paypal_express") {
                //for PayPal method use the same text as for delivery popup
                this.openPopup({...this.popupOptions.delivery, confirmUrl: confirmUrl});
            } else {
                this.openPopup({...this.popupOptions.shipping, confirmUrl: confirmUrl});
            }
        },

        /**
         * @returns {void}
         */
        deliveryClick: function () {
            const confirmUrl = this.deliveryUrl + `?subscription_id=${this.subscriptionId}`
                + `&subscription_payment=${this.subscriptionPayment}`;

            if (this.subscriptionPayment === "paypal_express") {
                this.openPopup({...this.popupOptions.delivery, confirmUrl: confirmUrl});
            } else {
                window.location.href = confirmUrl;
            }
        }
    });
});
