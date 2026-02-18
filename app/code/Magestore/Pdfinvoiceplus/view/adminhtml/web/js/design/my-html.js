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
    'Magestore_Pdfinvoiceplus/js/model/full-screen-loader',
    'mage/translate',
], function($, fullScreenLoader, $t) {
    window.MyHtml = {
        init: function (options) {
            this.options = options;
        },
        'saving': false,
        'save': function(callbackUrl) {
            this.options.saveData.html = this.toHtml();
            fullScreenLoader.startLoader();
            this.saving = true;
            $.ajax(this.options.saveHtmlUrl, {
                data: MyHtml.options.saveData,
                'type': "POST",
                'crossDomain': true,
            }).done(function (response) {
                if(response.error) {
                    alert(response.message);
                } else {
                    if (callbackUrl) {
                        window.location = callbackUrl;
                    }
                }
            }).always(function () {
                fullScreenLoader.stopLoader();
                MyHtml.saving = false;
            });

        },
        'toHtml': function() {
            //check editing autogrow or uploading image
            if ($('.autogrow form button[type=submit]').length || $('.insert-logo').length) {
                if (confirm($t('Do you want to discard unsaved changes?'))) {
                    $('.autogrow form button[type=submit]').trigger('click');
                    $('.insert-logo button[type=submit]').trigger('click'); //upload
                } else {
                    $('.autogrow form button[type=cancel]').trigger('click');
                    $('.insert-logo button[type=cancel]').trigger('click'); //cancel
                }
            }

            //get content html
            $html = $('#container-html').clone(true);
            $html.find('.control-ui').remove();
            $html.find('.ui-resizable-handle').remove();
            $html.find('.new-block').removeClass('new-block');
            $html.find('.ajaxupload > form').remove();

            //convert all rgb color to hex color
            function convert2hex(str) {
                function rgb2hex(rgb) {
                    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
                    return "#" +
                        ("0" + parseInt(rgb[1], 10).toString(16)).slice(-2) +
                        ("0" + parseInt(rgb[2], 10).toString(16)).slice(-2) +
                        ("0" + parseInt(rgb[3], 10).toString(16)).slice(-2);
                }
                return str.replace(/rgb\(\d+,\s*\d+,\s*\d+\)/g, function(matched) {
                    return rgb2hex(matched);
                });
            }
            return convert2hex($html.html());
        },
        resetDefault: function() {
            var check = setInterval(function(){
                if (!MyHtml.saving) {
                    clearInterval(check);
                    clearTimeout(wait);
                    window.location.href = MyHtml.options.syncInfoResetUrl;
                }
            },10);

            var wait = setTimeout(function() {
                clearInterval(check);
            }, 10000);
        }
    };

    return MyHtml;
});
