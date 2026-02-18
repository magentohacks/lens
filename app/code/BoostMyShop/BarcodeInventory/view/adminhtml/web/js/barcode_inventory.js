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

    $.BarcodeInventory = function()
    {
        this.KC_value = '';
        this.currentProduct = null;
        this.knownProducts = new Array();
        this.newStockLevels = new Array();
    };

    $.BarcodeInventory.prototype = {

        init: function (eProductInformationUrl, eCommitProductStockUrl, eImmediateSave, eDefaultMode)
        {
            this.ProductInformationUrl = eProductInformationUrl;
            this.CommitProductStockUrl = eCommitProductStockUrl;
            this.ImmediateSave = eImmediateSave;
            this.mode = eDefaultMode;


            //events handler
            $('.sbi-minus').on('click', {obj: this}, this.decreaseCurrentProductStock);
            $('.sbi-plus').on('click', {obj: this}, this.increaseCurrentProductStock);
            $('.sbi-mode').on('click', {obj: this}, this.setMode);

            $('.sbi-btn-commit').on('click', {obj: this}, this.commitCurrentProductStock);

            $('#btn-save').on('click', {obj: this}, this.save);

            $(document).on('keypress', {obj: this}, this.handleKey);

        },

        //********************************************************************* *************************************************************
        //
        waitForScan: function () {
            $('#div_product').hide();

            this.showInstruction(kTextScanProduct, false);

        },

        //**********************************************************************************************************************************
        //
        handleKey: function (evt) {

            //Dont process event if focuses control is text
            var focusedElt = evt.target.tagName.toLowerCase();
            if ((focusedElt == 'text') || (focusedElt == 'textarea'))
                return true;

            var keyCode = evt.which;
            if (keyCode != 13) {
                evt.data.obj.KC_value += String.fromCharCode(keyCode);
                evt.data.obj.barcodeDigitScanned();
            }
            else {
                evt.data.obj.scanProduct();
                evt.data.obj.KC_value = '';
            }

            return false;
        },

        //**********************************************************************************************************************************
        //
        scanProduct: function () {

            //init vars
            var barcode = this.KC_value;
            this.KC_value = '';

            $('#div_product').hide();

            //check if product already known
            if (this.knownProducts[barcode]) {
                this.playOk();
                this.currentProduct = this.knownProducts[barcode];
                this.processProduct();
                return true;
            }


            //launch ajax request to get product information
            var url = this.ProductInformationUrl;
            url = url.replace('[barcode]', barcode);

            //ajax request
            jQuery.ajax(
                {
                    url : url,
                    type : 'GET',
                    context: this
                }).done(function (result) {
                    if (!result.success) {
                        this.showMessage(result.msg, true);
                    }
                    else {

                        this.playOk();

                        //display products information
                        this.currentProduct = result;

                        //append product in cache
                        this.knownProducts[this.currentProduct.barcode] = this.currentProduct;

                        this.processProduct();
                    }
                });

        },

        //**********************************************************************************************************************************
        //
        processProduct: function () {
            this.showMessage(this.currentProduct.name);

            $('#product_name').html(this.currentProduct.name);
            $('#product_sku').html(this.currentProduct.sku);
            $('#product_image').attr("src", this.currentProduct.image_url);
            $('#product_stock').val(parseInt(this.currentProduct.qty));

            $('#div_product').show();

            //process depending of mode
            switch (this.mode) {
                case 'manual':
                    $('#product_stock').val(parseInt($('#product_stock').val()));
                    $('#div_product').show();
                    break;
                case 'increase':
                    $('#product_stock').val(parseInt($('#product_stock').val()) + 1);
                    this.commitCurrentProductStock();
                    break;
                case 'decrease':
                    if ($('#product_stock').val() > 0)
                        $('#product_stock').val(parseInt($('#product_stock').val()) - 1);
                    this.commitCurrentProductStock();
                    break;
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
        },

        //******************************************************************************
        //
        setMode: function (event) {
            event.data.obj.mode = event.srcElement.attributes[0].value;

            if (event.data.obj.mode == 'button')
                return false;

            $('.sbi-mode').removeClass('sbi-button-selected').addClass('sbi-button');

            $('#btn-mode-' + event.data.obj.mode).addClass('sbi-button-selected');

            event.data.obj.showMessage('Mode changed to ' + event.data.obj.mode);

        },

        //******************************************************************************
        //
        increaseCurrentProductStock: function () {
            $('#product_stock').val(parseInt($('#product_stock').val()) + 1);
        },

        //******************************************************************************
        //
        decreaseCurrentProductStock: function () {
            if ($('#product_stock').val() > 0)
                $('#product_stock').val(parseInt($('#product_stock').val()) - 1);
        },

        //******************************************************************************
        //
        commitCurrentProductStock: function (event) {

            var context = this;
            if (event)
                context = event.data.obj;

            //ajax request
            if (context.ImmediateSave) {

                //launch ajax request to get product information
                var url = context.CommitProductStockUrl;
                url = url.replace('XXX', context.currentProduct.id);
                url = url.replace('YYY', $('#product_stock').val());

                jQuery.ajax(
                    {
                        url : url,
                        type : 'GET',
                        context: context
                    }).done(function (result) {
                        if (!result.success) {
                            this.showMessage(result.msg, true);
                        }
                        else {
                            //confirm
                            this.showMessage(result.msg);
                            this.addHistory(this.currentProduct, $('#product_stock').val());

                            //update cache
                            this.knownProducts[this.currentProduct.barcode].qty = $('#product_stock').val();

                        }

                        $('#div_product').hide();
                    });

            }
            else {
                //append changes to array (for future mass save)
                context.newStockLevels[context.currentProduct.id] = $('#product_stock').val();
                context.addHistory(context.currentProduct, $('#product_stock').val());

                //update cache
                context.knownProducts[context.currentProduct.barcode].qty = $('#product_stock').val();

            }

        },

        //******************************************************************************
        //
        addHistory: function (product, newStockLevel) {

            //calculate difference
            var diff = newStockLevel - product.qty;
            if (diff > 0)
                diff = '+' + diff;

            var html = '<tr>';

            html += '<td><img src="' + product.image_url + '" height="30"></td>';
            html += '<td>' + product.barcode + '</td>';
            html += '<td class="sbi-table-cell">' + product.sku + '</td>';
            html += '<td class="sbi-table-cell">' + product.name + '</td>';
            html += '<td class="sbi-table-cell">' + diff + '</td>';
            html += '<td class="sbi-table-cell">' + newStockLevel + '</td>';

            html += '</tr>';

            $('#table_history tr:last').after(html);
        },

        //******************************************************************************
        //Save all changes (when immediate save is disabled)
        save: function (event) {

            //create a string with all new values
            var i;
            var string = '';
            for (i = 0; i < event.data.obj.newStockLevels.length; i++) {
                if (event.data.obj.newStockLevels[i] != undefined) {
                    string += i + '=' + event.data.obj.newStockLevels[i] + ';';
                }
            }
            if (string == '') {
                alert(kTextNoProductToSave);
                return false;
            }

            //store stirng in form and submit it
            $('#changes').val(string);
            $('#form_save').submit();
        }
    }

    return new $.BarcodeInventory();

});
