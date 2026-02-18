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
    'ko',
    'Magestore_Pdfinvoiceplus/js/model/form-data',
    'Magestore_Pdfinvoiceplus/js/action/build-form-url',
    'mage/translate',
    'jquery/ui',
], function($, ko, formData, buildFormUrlAction, $t) {
    "use strict";

    $.widget('magestore.editDesign', {
        options: {
            variables: null
        },

        _create: function() {
            var self = this, options = this.options;

            $.extend(this, {
                $form: $('#edit_form'),
                buttonData: $(self.element).data('button-data')
            });

            $(self.element).click(function () {
                if(formData.isChangeDesign()) {
                    if(confirm($t('You have selected a new design. Are you sure you want to change?'))) {
                        self.submitChangeDesign();
                    }
                } else {
                    self.submitChangeDesign();
                }
            });
        },

        submitChangeDesign: function () {
            var self = this, options = this.options;
            var params = $(self.element).data('button-data');
            params.back = 'edit_design';

            self.$form.prop('action', buildFormUrlAction(self.$form.prop('action'), params));
            self.$form.submit();
        },
    });

    return $.magestore.editDesign;
});
