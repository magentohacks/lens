define([
    "jquery",
    "mage/translate",
    "prototype",
    "Magento_Ui/js/modal/alert"
], function(jQuery, translate, prototype, alert){

    window.AdminProductSupplier = new Class.create();

    AdminProductSupplier.prototype = {

        initialize: function(){

        },

        init: function(saveUrl)
        {
            this.saveUrl = saveUrl;
        },

        saveAll: function()
        {
            var fields = $$('[name^="products["]');
            var data = Form.serializeElements(fields, true);
            data.FORM_KEY = window.FORM_KEY;

            jQuery.ajax({
                url: objProductSupplier.saveUrl,
                data: data,
                success: function (resp) {
                    document.location.href = document.location.href;
                },
                failure: function (resp) {
                    jQuery('#debug').html('An error occured.');
                }
            });
        },

        save: function(supId, productId)
        {
            var fields = $$('[name^="products[' + supId + '][' + productId + ']"]');
            var data = Form.serializeElements(fields, true);
            data.FORM_KEY = window.FORM_KEY;

            jQuery.ajax({
                url: objProductSupplier.saveUrl,
                data: data,
                success: function (resp) {

                },
                failure: function (resp) {
                    jQuery('#debug').html('An error occured.');
                }
            });

        }

    };

});
