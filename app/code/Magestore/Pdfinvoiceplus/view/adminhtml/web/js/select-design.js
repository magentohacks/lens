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
    'Magestore_Pdfinvoiceplus/js/model/form-data',
    'tinyBox',
    'jquery/ui',
], function($, formData) {
    "use strict";

    $.widget('magestore.selectDesign', {
        options: {
            btnSelectDesign: '',
        },

        _create: function() {
            var self = this, options = this.options;

            $.extend(this, {
                $btnSelectDesign: $(options.btnSelectDesign),
                $pdftemplateGeneralFieldset: $('#general_fieldset'),
                $inputTemplateTypeId: $('[name="template_type_id"]'),
            });

            self.applyTemplate();

            self.$btnSelectDesign.click(function () {
                self.showDialog();
            });
        },

        showDialog: function () {
            var self = this, options = this.options;
            TINY.box.show({
                html: $(self.element).html(),
                boxid: 'frameless',
                width: 460,
                height: 310,
                fixed: true,
                maskid: 'bluemask',
                maskopacity: 40,
                openjs: function() {
                    $('.tbox .pdf-invoice-popup-load-template').click(function () {
                        formData.currentTemplateId($(this).data('template-type'));
                        TINY.box.hide();
                        self.applyTemplate();
                    });
                }
            });
        },

        applyTemplate: function () {
            var self = this, options = this.options;

            if(!self.$pdftemplateGeneralFieldset.find('.image-template-view').length) {
                self.$pdftemplateGeneralFieldset.append('<div id="image-template-view" class="image-template-view"></div>');
            }

            var $imageTemplateView = self.$pdftemplateGeneralFieldset.find('.image-template-view');
            if(formData.currentTemplateId()) {
                var $img = $(self.element).find('.img-template-type-' + formData.currentTemplateId());
                $imageTemplateView.html($img.clone());
                $imageTemplateView.show();
            } else {
                $imageTemplateView.html('');
                $imageTemplateView.hide();
            }
        },
    });

    return $.magestore.selectDesign;
});
