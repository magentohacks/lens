define(
    [
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/model/error-processor',
        'uiRegistry',
        'Magento_Checkout/js/model/quote',
        'mage/storage'
    ],
    function (
        totals,
        errorProcessor,
        registry,
        quote,
        storage
    ) {
        "use strict";

        return function (quoteTotals) {
            waitForTotals().then(function () {
                    totals.isLoading(true);

                    storage.get('/checkout/ajax/getItemsData').done(
                        function (result) {
                            if (!result) {
                                window.location.reload();
                            }

                            if (result.image_data) {
                                registry.get('checkout.sidebar.summary.cart_items.details.thumbnail').imageData
                                    = result.image_data;
                            }

                            if (result.options_data) {
                                var options = result.options_data;
                                quoteTotals.items.forEach(function (item) {
                                    item.amcheckout = options[item.item_id];
                                });
                            }
                            quote.setTotals(quoteTotals);
                        }
                    ).fail(
                        function (response) {
                            errorProcessor.process(response);
                        }
                    ).always(
                        function () {
                            totals.isLoading(false);
                        }
                    );
                }
            );
        };

        function waitForTotals() {
            return new Promise(function (resolve) {
                if (!totals.isLoading()) {
                    resolve();
                } else {
                    const subscription = totals.isLoading.subscribe(function (loading) {
                        if (!loading) {
                            subscription.dispose();
                            resolve();
                        }
                    });
                }
            });
        }
    });
