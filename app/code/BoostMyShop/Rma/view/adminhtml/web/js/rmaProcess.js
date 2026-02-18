define([
    "jquery",
    "mage/translate",
    "prototype",
    "Magento_Ui/js/modal/alert"
], function(jQuery, translate, prototype, alert){

    window.AdminRmaProcess = new Class.create();

    AdminRmaProcess.prototype = {

        initialize: function(){
            this.data;
        },

        init: function(data)
        {
            this.data = data;
        },

        update: function()
        {
            this.updateTotalRefunded();
        },

        updateTotalRefunded: function()
        {
            var total = 0;

            if (jQuery('#shipping_refund').val() == "1")
                total += parseFloat(this.data.shipping);

            jQuery.each( this.data.items, function( key, item ) {
                total += parseFloat(item.price) * parseInt(jQuery('#items_' + item.id + '_refund').val());
            });

            total += parseFloat(jQuery('#adjustment').val());
            total -= parseFloat(jQuery('#fee').val());

            jQuery('#total_refunded').html(total);
        },

        submit: function()
        {
            if (parseInt(jQuery('#total_refunded').html()) < 0)
                alert({content: 'You can not process a negative refund.'});
            else
                document.getElementById('edit_form').submit();
        }

    };

});
