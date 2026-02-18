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
    'Magestore_Pdfinvoiceplus/js/form/variables',
    'jquery/ui',
], function($, ko, formData) {
    "use strict";

    $.widget('magestore.insertVariable', {
        options: {
            variables: null
        },

        _create: function() {
            var self = this, options = this.options;
            var variables = ko.observable(null);
            $(self.element).click(function () {
                MagentovariablePlugin.loadChooser(options.url, options.target, variables);
            });
        },
    });

    return $.magestore.insertVariable;
});
