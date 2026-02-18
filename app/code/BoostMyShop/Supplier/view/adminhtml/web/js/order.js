
define([
    "jquery",
    "mage/translate",
    "prototype"
], function(jQuery, confirm, alert){

    window.AdminOrder = new Class.create();

    AdminOrder.prototype = {

        initialize : function(data){
            this.productToAddQuantities = {};
            this.products = {};


            jQuery('#edit_form').on('submit', this.saveProductsToAdd.bind(this));
        },

        toggleProductToAddQty: function(productId)
        {
            $('qty_' + productId).disabled = !$('check_' + productId).checked;
            $('qty_' + productId).value = (($('check_' + productId).checked ? '1' : ''));

            this.changeProductToAddQty(productId);
        },

        changeProductToAddQty: function(productId)
        {
            this.productToAddQuantities[productId] = $('qty_' + productId).value;
        },

        /**
         * Populate products to add in textbox before form submission
         */
        saveProductsToAdd: function()
        {
            if (!$('po_products_to_add'))
                return;

            $('po_products_to_add').value = '';

            jQuery.each( this.productToAddQuantities, function( key, value ) {
                $('po_products_to_add').value += key + '=' + value + ';';
            });
        }


    };

});
