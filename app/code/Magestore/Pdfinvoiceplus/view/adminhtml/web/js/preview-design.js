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
    'tinyBox',
    'jquery/ui'
], function($) {
    "use strict";

    $.widget('magestore.previewDesign', {
        options: {
        },

        _create: function() {
            var self = this, options = this.options;

            $.extend(this, {
                buttonData: $(self.element).data('button-data')
            });
            $(self.element).click(function () {
                self.showDesign(self.getUrlPreviewDesign());
            });
        },

        getUrlPreviewDesign: function () {
            return this.options.url + 'template_id/' + this.buttonData.template_id + '/design_type/' + this.buttonData.design_type
        },

        showDesign: function (url) {
            TINY.box.show({
                iframe: url,
                boxid: 'frameless',
                animate: false,
                width: 900,
                height: 650,
                fixed: false,
                maskid: 'bluemask',
                maskopacity: 40,
                openjs: function(){},
                closejs: function(){}
            });
        },
    });

    return $.magestore.previewDesign;
});