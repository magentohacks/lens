define([
    "jquery",
    'Magento_Ui/js/modal/alert',
    "jquery/ui",
    'mage/cookies',
], function ($, alert) {
    'use strict';
    $.widget('mage.lensoption', {
        _create: function () {
            var self = this;
            var options = self.options;
            var reorderUrl = options.reorderUrl;
            var checkoutUrl = options.checkoutUrl;
            $(document).on('click','#reorder-history-new', function(){
                var orderId = $(this).attr('data-id');
                $.ajax({
                    showLoader: true,
                    type: "POST",
                    dataType: "json",
                    url: reorderUrl,
                    data: {
                        'order_id': orderId,
                        'form_key': $.mage.cookies.get('form_key'),
                    },
                    cache: false,
                    success: function(data) {
                        console.log(data);
                        if (data.success) {
                            window.location.replace(checkoutUrl);
                        } else {
                            alert({
                                content: $.mage.__('The product Does Not exist. Sorry for inconvience. Please proceed with a new order.'),
                            });
                        }
                        return true
                    }
                });
            });
            
        }
    });
    return $.mage.lensoption;
});