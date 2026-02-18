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
var magestore = magestore || {};
magestore.config = function(baseUrl) {
    (function(require) {
        require.config({"baseUrl":baseUrl});
        (function() {
            /**
             * Copyright Â© 2015 Magento. All rights reserved.
             * See COPYING.txt for license details.
             */

            var config = {
                "shim": {
                    "extjs/ext-tree": [
                        "prototype"
                    ],
                    "extjs/ext-tree-checkbox": [
                        "extjs/ext-tree",
                        "extjs/defaults"
                    ],
                    "jquery/editableMultiselect/js/jquery.editable": [
                        "jquery"
                    ]
                },
                "bundles": {
                    "js/theme": [
                        "globalNavigation",
                        "globalSearch",
                        "modalPopup",
                        "useDefault",
                        "loadingPopup",
                        "collapsable"
                    ]
                },
                "map": {
                    "*": {
                        "translateInline":      "mage/translate-inline",
                        "form":                 "mage/backend/form",
                        "button":               "mage/backend/button",
                        "accordion":            "mage/accordion",
                        "actionLink":           "mage/backend/action-link",
                        "validation":           "mage/backend/validation",
                        "notification":         "mage/backend/notification",
                        "loader":               "mage/loader_old",
                        "loaderAjax":           "mage/loader_old",
                        "floatingHeader":       "mage/backend/floating-header",
                        "suggest":              "mage/backend/suggest",
                        "mediabrowser":         "jquery/jstree/jquery.jstree",
                        "tabs":                 "mage/backend/tabs",
                        "treeSuggest":          "mage/backend/tree-suggest",
                        "calendar":             "mage/calendar",
                        "dropdown":             "mage/dropdown_old",
                        "collapsible":          "mage/collapsible",
                        "menu":                 "mage/backend/menu",
                        "jstree":               "jquery/jstree/jquery.jstree",
                        "details":              "jquery/jquery.details",
                        "uiClass":              "Magento_Ui/js/lib/core/class",
                        "uiEvents":             "Magento_Ui/js/lib/core/events",
                        "uiRegistry":           "Magento_Ui/js/lib/registry/registry"
                    }
                },
                "deps": [
                    "js/theme",
                    "mage/backend/bootstrap",
                    "mage/adminhtml/globals"
                ],
                "paths": {
                    "jquery/ui": "jquery/jquery-ui-1.9.2",
                    "ui/template": "Magento_Ui/templates"
                }
            };

            require.config(config);
        })();
        (function() {
            /**
             * Copyright Â© 2015 Magento. All rights reserved.
             * See COPYING.txt for license details.
             */

            var config = {
                "waitSeconds": 0,
                "map": {
                    "*": {
                        "mageUtils": "mage/utils/main",
                        "ko": "knockoutjs/knockout",
                        "knockout": "knockoutjs/knockout"
                    }
                },
                "shim": {
                    "jquery/jquery-migrate": ["jquery"],
                    "jquery/jquery.hashchange": ["jquery", "jquery/jquery-migrate"],
                    "jquery/jstree/jquery.hotkeys": ["jquery"],
                    "jquery/hover-intent": ["jquery"],
                    "mage/adminhtml/backup": ["prototype"],
                    "mage/captcha": ["prototype"],
                    "mage/common": ["jquery"],
                    "mage/new-gallery": ["jquery"],
                    "mage/webapi": ["jquery"],
                    "jquery/ui": ["jquery"],
                    "MutationObserver": ["es6-collections"],
                    "tinymce": {
                        "exports": "tinymce"
                    },
                    "moment": {
                        "exports": "moment"
                    },
                    "matchMedia": {
                        "exports": "mediaCheck"
                    },
                    "jquery/jquery-storageapi": {
                        "deps": ["jquery/jquery.cookie"]
                    }
                },
                "paths": {
                    "jquery/validate": "jquery/jquery.validate",
                    "jquery/hover-intent": "jquery/jquery.hoverIntent",
                    "jquery/file-uploader": "jquery/fileUploader/jquery.fileupload-fp",
                    "jquery/jquery.hashchange": "jquery/jquery.ba-hashchange.min",
                    "prototype": "legacy-build.min",
                    "jquery/jquery-storageapi": "jquery/jquery.storageapi.min",
                    "text": "mage/requirejs/text",
                    "domReady": "requirejs/domReady",
                    "tinymce": "tiny_mce/tiny_mce_src"
                },
                "deps": [
                    "jquery/jquery-migrate"
                ]
            };

            require(['jquery'], function ($) {
                $.noConflict();
            });

            require.config(config);
        })();
    })(require);
};