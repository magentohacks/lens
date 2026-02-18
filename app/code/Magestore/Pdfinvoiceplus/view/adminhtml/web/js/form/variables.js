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
    'Magento_Variable/variables',
], function($) {
    $.extend(window.MagentovariablePlugin, {
        loadChooser: function(url, textareaId, variables) {
            this.textareaId = textareaId;
            if (variables() == null) {
                new Ajax.Request(url, {
                    parameters: {},
                    onComplete: function (transport) {
                        if (transport.responseText.isJSON()) {
                            Variables.variablesContent = null;
                            Variables.init(null, 'MagentovariablePlugin.insertVariable');
                            variables(transport.responseText.evalJSON());
                            this.openChooser(variables());
                        }
                    }.bind(this)
                });
            } else {
                this.openChooser(variables());
            }

            return;
        }
    });
});
