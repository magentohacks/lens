/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_QuickbooksOnline
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery',
    'Mageplaza_QuickbooksOnline/js/view/variables'
], function ($, QuickbooksVariables) {
    "use strict";

    $.widget('mageplaza.quickbooks', {

        _create: function () {
            var self = this;

            if (!this.options.isEdit) {
                this.detachMappingObject();
                this.initObserve();
            } else {
                self.initVariables();
                this.reindexPayment();
            }
        },

        detachMappingObject: function () {
            var magentoObject          = $("#magento_object").val();
            var optionQuickbooksModule = '';
            var websiteIds             = $('#website_ids');

            $.each(this.options.mappingObject[magentoObject], function (index, module) {
                optionQuickbooksModule += '<option value="' + module.value + '">' + module.label + '</option>';
            });

            if (magentoObject === 'paymentMethod') {
                websiteIds.removeAttr('multiple');
                websiteIds.removeAttr('size');
                websiteIds.switchClass('admin__control-select', 'admin__control-multiselect');
            } else {
                websiteIds.attr('size', 10);
                websiteIds.attr('multiple', 'multiple');
                websiteIds.switchClass('admin__control-select', 'admin__control-multiselect');
            }

            $("#quickbooks_module").html(optionQuickbooksModule);
        },

        checkMapping: function () {
            var self = this;

            $("#magento_object").change(function () {
                self.detachMappingObject();
            });
        },

        validateWebsite: function () {
            var websiteIdsElement = $('#website_ids');

            $('#website_ids-error').remove();
            if (websiteIdsElement.val() === null) {
                websiteIdsElement.parent().append(this.getErrorElement());

                return false;
            }

            return true;
        },

        getErrorElement: function () {
            return '<label class="mage-error" id="website_ids-error">' + this.options.errorLabel + '</label>';
        },

        /**
         * Init observe
         */
        initObserve: function () {
            this.checkMapping();
            this.initSync();
        },

        initVariables: function () {
            var self = this;

            $(".insert_variable").click(function () {
                if (self.options.variables) {
                    var fieldTarget = $(this).attr('target');

                    QuickbooksVariables.setEditor(fieldTarget + '-value');
                    QuickbooksVariables.openVariableChooser(self.options.variables);
                }
            });
        },

        initSync: function () {
            var self = this;

            $("#sync-next").click(function () {
                if (self.validateWebsite()) {
                    var params = {
                        website_ids: $('#website_ids').val(),
                        magento_object: $('#magento_object').val(),
                        quickbooks_module: $('#quickbooks_module').val()
                    };

                    $.ajax({
                        method: 'POST',
                        url: self.options.mappingUrl,
                        data: params,
                        showLoader: true,
                        success: function (response) {
                            if (response.canMapping) {
                                $("#mapping-body").append(response.mapping_html);
                                if (response.module === 'paymentMethod') {
                                    $('#sync_tabs_mapping_content').html(response.payment_html);
                                    $('li[data-ui-id|="mageplaza-quickbooks-code-tabs-tab-item-condition"]').remove();
                                    $('li[data-ui-id|="mageplaza-quickbooks-code-tabs-tab-item-mapping"]').remove();
                                    $('div#Conditions').remove();
                                }
                                $('#general>legend>span').text(self.options.generalLabel);
                                $('.page-columns .side-col').show();
                                $('.admin__field.field.field-status,.admin__field.field.field-name,' +
                                    '.admin__field.field.field-priority').show();
                                $('button#save,button#reset,button#save_and_continue').show();
                                $('#container').attr('style', 'width:calc( (100%) * 0.75 - 30px )');
                                $("#sync-next").hide();
                                $('#magento_object,#quickbooks_module,#website_ids').attr('readonly', 'readonly')
                                .css('pointer-events', 'none');
                                self.options.variables = JSON.parse(response.variables);
                                self.initVariables();
                                $('#mp_mapping').trigger('contentUpdated');
                            } else {
                                location.reload();
                            }
                        }
                    });
                }
            });
        },

        reindexPayment: function () {
            var self = this;

            $(document).on('click', '#btn-check-update', function () {
                location.href = self.options.reindexPaymentUrl;
            });
        }
    });

    return $.mageplaza.quickbooks;
});
