/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
define([
    'jquery',
    'MyHtml',
    'MyTable',
    'Variable',
    'mage/translate',
    'jquery/ui',
    'jeditable',
    'editContent',
    'jeditableAjaxupload',
    'jeditableAjaxuploadLogo',
], function($, MyHtml, MyTable, Variable,  $t) {
    "use strict";

    $.widget('magestore.designContainer', {
        options: {
            indicatorImageUrl: '',
            ajaxUploadLogoUrl: '',
            variableOption: {}
        },

        _create: function () {
            var self = this, options = this.options;
            console.log(options);

            $(".autogrow").editable(function(value) {
                return value;
            }, {
                'indicator': '<img src="'+ options.indicatorImageUrl + '" />',
                'type': "autogrow",
                'event': "dblclick",
                'submit': 'Ok',
                'cancel': 'Cancel',
                'tooltip': "Click to edit...",
                'placeholder': '',
                'onblur': "ignore"
            });

            $(".ajaxupload-logo").editable(options.ajaxUploadLogoUrl, {
                'indicator': '<img src="'+ options.indicatorImageUrl + '" />',
                'type': 'ajaxupload-logo',
                'submit': 'Upload',
                'cancel': 'Cancel',
                'tooltip': "Click to upload...",
                'id': "insert-logo",
                'name': "insert-logo",
                'cssclass': "insert-logo",
                'placeholder': '<div class="logo-ui control-ui">Insert your logo here<div>',
                'oncreated': function(e, opt) {
                    //check view port
                    var form = $(e).find('form');
                    var offsetXLeft = form.offset().left + form.width();
                    if (offsetXLeft > $(window).width()) {
                        var left = parseInt(form.css('margin-left'));
                        form.css('margin-left', left - offsetXLeft + $(window).width() - 20);
                    } else if (form.offset().left < 0) {
                        form.css('margin-left', 10);
                    }
                },
                imageUploadUrl: options.ajaxUploadLogoUrl
            });

            MyTable.init('table-item', options.variableOption);

            Variable.init(options.variableOption);

            //make editable for table
            $('th[contenteditable=true], td[contenteditable=true], th.contenteditable, td.contenteditable').editContent();
            //fix conflic contenteditable
            $('.contenteditable').not('th,td').attr('contenteditable', 'true');

            //var tooltip text
            window.tooltipText = $t('Hold to move!');


            $(self.element).on('add_block_after', function(event, $newBlock, timeNow) {
                $newBlock.attr('contextmenu-type', "main");
                $newBlock.resizable({
                    //'containment': "parent",
                    'handles': {
                        's': $('#s_handle_block_' + timeNow).get(0),
                        'e': $('#e_handle_block_' + timeNow).get(0),
                        'se': $('#se_handle_block_' + timeNow).get(0)
                    },
                    'minWidth': 100,
                    'minHeight': 16,
                    create: function() {
                        $(this).addClass('new-block').width(200).height("auto");
                        $($(this)[0]).children('.ui-resizable-handle').mouseover(function(el) {
                            var parent = $(el.target).parent().parent();
                            parent.css('height', parent.height() + "px");
                        });
                    },
                    stop: px2percent
                });
                Variable.initMenu();
            });
        }
    });

    return $.magestore.designContainer;
});
