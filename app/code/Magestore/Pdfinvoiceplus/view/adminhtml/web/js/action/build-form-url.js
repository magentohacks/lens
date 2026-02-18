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
define(['jquery'], function($) {
    "use strict";
    var activedTabSelector = '#tag_tabs .ui-tabs-active .tab-item-link';

    function getActiveTabName () {
        return $(activedTabSelector).prop('name');
    }

    return function (action, params) {
        params.active_tab = getActiveTabName();
        for(var param in params) {
            if(params[param] && typeof params[param] != "function") {
                action += param + '/' + params[param] + '/';
            }
        }
        return action;
    };
});
