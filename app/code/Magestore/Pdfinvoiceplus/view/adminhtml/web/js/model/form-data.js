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
 * @package     Magestore_OneStepCheckout
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

define(
    [
        'jquery',
        "underscore",
        'ko',
    ],
    function ($, _, ko) {
        'use strict';

        var $templateId = $('#template_id'),
            $barcodeType = $('#barcode_type'),
            $showBarcode = $('#barcode');

        function applyShowBarcodeType() {
            if($showBarcode.val() == 1) {
                $('.field-barcode_type').show();
            } else {
                $('.field-barcode_type').hide();
            }
        }

        $(document).ready(function($){
            $showBarcode.change(function(){
                applyShowBarcodeType();
            });
            applyShowBarcodeType();
            $barcodeType.prop('disabled', !!$templateId.val());
        });

        var IS_CHANGED_DESIGN = 1,
            IS_NOT_CHANGED_DESIGN = 0;

        var $inputTemplateTypeId = $('[name="template_type_id"]'),
            $inputFlagChangeDesign = $('[name="flag_change_design"]'),
            originTemplateTypeId = ko.observable($inputTemplateTypeId.val()),
            currentTemplateId = ko.observable($inputTemplateTypeId.val()),
            isChangeDesign = ko.observable(false),
            elementDisabledSelector = '.element-disabled, [name="company_logo"], [name="company_logo[delete]"]';

        currentTemplateId.subscribe(function(val){
            $inputTemplateTypeId.val(val);
            isChangeDesign(originTemplateTypeId() != val);
            $inputFlagChangeDesign.val(isChangeDesign() ? IS_CHANGED_DESIGN : IS_NOT_CHANGED_DESIGN);
            $(elementDisabledSelector).prop('disabled', !isChangeDesign());
            applyShowBarcodeType(isChangeDesign());
            if(isChangeDesign()) {
                $barcodeType.prop('disabled', false);
            } else{
                $barcodeType.prop('disabled', !!$templateId.val());
            }
        });

        return {
            isChangeDesign: isChangeDesign,
            originTemplateTypeId: originTemplateTypeId,
            currentTemplateId: currentTemplateId,
        };
    }
);
