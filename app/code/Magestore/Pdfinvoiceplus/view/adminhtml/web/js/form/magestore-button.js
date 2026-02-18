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
    'Magestore_Pdfinvoiceplus/js/action/build-form-url',
    'mage/translate',
    'jquery/ui',
], function($, formData, buildFormUrlAction, $t) {
    "use strict";

    $.widget('magestore.magestoreButton', {
        options: {
            back: '',
            target: '',
        },

        _create: function() {
            var self = this, options = this.options;
            $.extend(this, {
                $formTarget: $(this.options.target),
                activedTabSelector: '#tag_tabs .ui-tabs-active .tab-item-link',
                $inputTemplateTypeId: $('[name="template_type_id"]')
            });

            $(self.element).click(function(){
                if(self.validate()) {
                    if(formData.isChangeDesign()) {
                        if(formData.originTemplateTypeId()) {
                            if(confirm($t('You have changed to other design! The old template data will be lost. Are you sure?'))) {
                                self.submitForm();
                            }
                        } else {
                            self.submitForm();
                        }
                    } else {
                        self.submitForm();
                    }

                }
            });
        },

        submitForm: function () {
            var self = this, options = this.options;
            var params = {
                back: options.back
            };
            self.$formTarget.prop('action', buildFormUrlAction(self.$formTarget.prop('action'), params));

            self.$formTarget.submit();
        },

        validate: function () {
            if(!this.$inputTemplateTypeId.val()) {
                alert($t("Can't save. You must select design first!"));

                return false;
            }

            return true;
        }
    });

    return $.magestore.magestoreButton;
});
