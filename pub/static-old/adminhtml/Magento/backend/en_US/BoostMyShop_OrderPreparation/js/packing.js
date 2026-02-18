/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    "jquery",
    "jquery/ui",
    "domReady!"
], function ($) {
    'use strict';

    $.Packing = function()
    {
        this.KC_value = '';
    };

    $.Packing.prototype = {


        ping: function() {
            alert('pong');
        },

        init: function (eSelectOrderByIdUrl, eItemIds, eOrderIds, eMode, autoDownloadUrls, eAllowPartialPacking)
        {
            this.selectOrderByIdUrl = eSelectOrderByIdUrl;
            this.itemIds = eItemIds;
            this.orderIds = eOrderIds;
            this.mode = eMode;
            this.allowPartialPacking = eAllowPartialPacking;

            $(document).on('keypress', {obj: this}, this.handleKey);
            $('#select-order').on('change', {obj: this}, this.selectOrderFromMenu);

            this.updateStatuses();

            if (autoDownloadUrls)
                this.download(autoDownloadUrls);

            return this;
        },

        download: function (autoDownloadUrls) {
            autoDownloadUrls.forEach(function(url) {
                if (url) {
                    $('<iframe src="' + url + '" frameborder="0" scrolling="no" style="display: none;"></iframe>').appendTo('#iframe-container');
                }
            });
        },

        //********************************************************************* *************************************************************
        //
        selectOrderFromMenu: function (evt) {
            var url = evt.data.obj.selectOrderByIdUrl;
            var orderInProgressId = $('#select-order option:selected').val();
            if (orderInProgressId)
            {
                url = url.replace('[order_id]', orderInProgressId);
                document.location.href = url;
            }
        },

        //********************************************************************* *************************************************************
        //
        waitForScan: function () {
            $('#div_product').hide();

            if (this.mode == 'pack_order')
                this.showInstruction('Scan product barcode', false);
            else
                this.showInstruction('Scan order barcode', false);
        },


        //**********************************************************************************************************************************
        //
        handleKey: function (evt) {

            //Dont process event if focuses control is text
            var focusedElt = evt.target.tagName.toLowerCase();
            if ((focusedElt == 'text') || (focusedElt == 'textarea') || (focusedElt == 'input'))
                return true;

            var keyCode = evt.which;
            if (keyCode != 13) {
                evt.data.obj.KC_value += String.fromCharCode(keyCode);
                evt.data.obj.barcodeDigitScanned();
            }
            else {
                if (evt.data.obj.mode == 'pack_order')
                    evt.data.obj.scanProduct();
                else
                    evt.data.obj.scanOrder();
                evt.data.obj.KC_value = '';
            }

            return false;
        },

        //**********************************************************************************************************************************
        //Quantity buttons
        qtyMin: function(itemId)
        {
            $('#qty_packed_' + itemId).val(0);
            this.updateStatuses();
        },
        qtyMax: function(itemId)
        {
            $('#qty_packed_' + itemId).val($('#qty_to_ship_' + itemId).val());
            this.updateStatuses();
        },
        qtyDecrement: function(itemId)
        {
            if ($('#qty_packed_' + itemId).val() > 0)
                $('#qty_packed_' + itemId).val(parseInt($('#qty_packed_' + itemId).val()) - 1);
            this.updateStatuses();
        },
        qtyIncrement: function(itemId)
        {
            if ($('#qty_packed_' + itemId).val() < $('#qty_to_ship_' + itemId).val())
                $('#qty_packed_' + itemId).val(parseInt($('#qty_packed_' + itemId).val()) + 1);
            this.updateStatuses();
        },

        //**********************************************************************************************************************************
        //
        updateStatuses: function()
        {
            this.itemIds.forEach(function(itemId) {
                var qtyPacked = $('#qty_packed_' + itemId).val();
                var qtyToShip = $('#qty_to_ship_' + itemId).val();
                var classes = '';
                var title = '';
                if (qtyPacked < qtyToShip) {
                    classes = 'packing-status-partial';
                    title = (qtyToShip - qtyPacked) + ' missing';
                }
                if (qtyToShip == qtyPacked) {
                    classes = 'packing-status-ok';
                    title = "Packed";
                }
                if (qtyPacked == 0) {
                    classes = 'packing-status-none';
                    title= 'Not packed';
                }

                $('#status_' + itemId).attr('class', "packing-status" + " " + classes);
                $('#status_' + itemId).html(title);
            });
        },

        //**********************************************************************************************************************************
        //
        scanOrder: function(){
            var orderIncrementId = this.KC_value;
            this.KC_value = '';

            var orderInProgressId = '';
            for (var key in this.orderIds) {
                if (this.orderIds[key] == orderIncrementId)
                    orderInProgressId = key;
            }

            if (!orderInProgressId)
                this.showMessage('This order is not available', true);
            else
            {
                var url = this.selectOrderByIdUrl;
                url = url.replace('[order_id]', orderInProgressId);
                document.location.href = url;
            }
        },

        //**********************************************************************************************************************************
        //
        scanProduct: function () {

            var barcode = this.KC_value;
            this.KC_value = '';

            //check barcode
            var itemId = this.getItemIdFromBarcode(barcode);
            if (!itemId)
            {
                this.showMessage('Incorrect Product Barcode', true);
                return false;
            }

            //check quantity
            var remainingQuantity = parseInt($('#qty_to_ship_' + itemId).val()) - parseInt($('#qty_packed_' + itemId).val());
            if (remainingQuantity == 0)
            {
                this.showMessage('Product already packed !', true);
                return false;
            }

            this.playOk();
            this.qtyIncrement(itemId);

        },

        //******************************************************************************
        //
        commitPacking: function() {

            if (!this.isCompletelyPacked() && !this.allowPartialPacking)
            {
                this.showMessage('Packing is not complete, please pack all products !', true);
                return false;
            }

            jQuery('#frm_products').submit();

        },


        //******************************************************************************
        //
        isCompletelyPacked: function() {
            for (var key in this.itemIds) {
                var itemId = this.itemIds[key];
                if (itemId > 0) {
                    var qtyPacked = $('#qty_packed_' + itemId).val();
                    var qtyToShip = $('#qty_to_ship_' + itemId).val();
                    if (qtyPacked < qtyToShip)
                        return false;
                }
            }
            return true;
        },

        //**********************************************************************************************************************************
        //
        getItemIdFromBarcode: function(barcode){
            for (var key in this.itemIds) {
                if ($('#barcode_' + this.itemIds[key]).val() == barcode)
                    return this.itemIds[key];
            }
        },

        //**********************************************************************************************************************************
        //
        barcodeDigitScanned: function () {
            this.showMessage(this.KC_value);
        },

        //******************************************************************************
        //
        showMessage: function (text, error) {
            if (text == '')
                text = '&nbsp;';

            if (error)
                text = '<font color="red">' + text + '</font>';
            else
                text = '<font color="green">' + text + '</font>';

            $('#div_message').html(text);
            $('#div_message').show();

            if (error)
                this.playNok();

        },

        //******************************************************************************
        //
        hideMessage: function () {
            $('#div_message').hide();
        },


        //******************************************************************************
        //display instruction for current
        showInstruction: function (text) {
            $('#div_instruction').html(text);
            $('#div_instruction').show();
        },

        //******************************************************************************
        //
        hideInstruction: function () {
            $('#div_instruction').hide();
        },

        playOk: function()
        {
            $("#audio_ok").get(0).play();
        },

        playNok: function ()
        {
            $("#audio_nok").get(0).play();
        }

    }

    return new $.Packing();

});
