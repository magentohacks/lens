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
(function() {
    var config = {
        map: {
            '*': {
                MyHtml: 'Magestore_Pdfinvoiceplus/js/design/my-html',
                MyTable: 'Magestore_Pdfinvoiceplus/js/design/my-table',
                Variable: 'Magestore_Pdfinvoiceplus/js/design/variable',
                editContent: 'Magestore_Pdfinvoiceplus/js/design/widget/edit-content',
                menuWrapper: 'Magestore_Pdfinvoiceplus/js/design/widget/menu-wrapper',
                designContainer: 'Magestore_Pdfinvoiceplus/js/design/widget/design-container',
                libDragtable: 'Magestore_Pdfinvoiceplus/js/lib/dragtable/extend.dragtable',
                jeditableAjaxupload: 'Magestore_Pdfinvoiceplus/js/lib/jquery.jeditable.ajaxupload',
                jeditableAjaxuploadLogo: 'Magestore_Pdfinvoiceplus/js/lib/jquery.ajaxuploadlogo',
            }
        },
        paths: {
            akottrDragtable: 'Magestore_Pdfinvoiceplus/js/lib/dragtable/jquery.dragtable',
            jqueryUiDraggable: 'Magestore_Pdfinvoiceplus/js/lib/dragtable/jquery.ui.draggable',
            jeditable: 'Magestore_Pdfinvoiceplus/js/lib/jquery.jeditable',
            jeditableAutogrow: 'Magestore_Pdfinvoiceplus/js/lib/jquery.jeditable.autogrow',
            autogrow: 'Magestore_Pdfinvoiceplus/js/lib/jquery.autogrow',
            ajaxfileupload: 'Magestore_Pdfinvoiceplus/js/lib/jquery.ajaxfileupload',
            contextmenu: 'Magestore_Pdfinvoiceplus/js/lib/jquery.contextmenu',
            dlmenu: 'Magestore_Pdfinvoiceplus/js/lib/listmenucolor/jquery.dlmenu',
            modernizrCustom: 'Magestore_Pdfinvoiceplus/js/lib/listmenucolor/modernizr.custom',
            colResizable: 'Magestore_Pdfinvoiceplus/js/lib/colResizable-1.3.min',
            spectrum: 'Magestore_Pdfinvoiceplus/js/lib/spectrum/spectrum',
            spectrumDocs: 'Magestore_Pdfinvoiceplus/js/lib/spectrum/docs/docs',
            spectrumColoritems: 'Magestore_Pdfinvoiceplus/js/lib/spectrum/docs/coloritems',
            spectrumDocsTitle: 'Magestore_Pdfinvoiceplus/js/lib/spectrum/docs/docs-title',
            spectrumDocsText: 'Magestore_Pdfinvoiceplus/js/lib/spectrum/docs/docs-text',
            'jquery/tinyMce': 'Magestore_Pdfinvoiceplus/js/lib/jquery.tinymce',
            //'jquery/tinyMce': 'tiny_mce/jquery.tinymce',
        },
        shim: {
            'akottrDragtable': ['jquery', 'jquery/ui'],
            'jqueryUiDraggable': ['jquery', 'jquery/ui'],
            'jeditable': ['jquery'],
            'jeditableAutogrow': ['jquery', 'jeditable'],
            'autogrow': ['jquery'],
            'ajaxfileupload': ['jquery'],
            'contextmenu': ['jquery'],
            'dlmenu': ['jquery', 'modernizrCustom'],
            modernizrCustom: {
                exports: "Modernizr"
            },
            'colResizable': ['jquery', 'jquery/jquery-migrate'],
            'spectrum': ['jquery'],
            'spectrumDocs': ['jquery', 'spectrum'],
            'spectrumColoritems': ['jquery', 'spectrum'],
            'spectrumDocsTitle': ['jquery', 'spectrum'],
            'spectrumDocsText': ['jquery', 'spectrum'],
            'jquery/tinyMce': ['jquery']
        },
    };
    require.config(config);
})(require);

require([
    'jeditable',
    'jeditableAutogrow',
    'autogrow',
    'ajaxfileupload',
    'jeditableAjaxupload',
    'jeditableAjaxuploadLogo',
    'colResizable',
    'spectrum',
    'spectrumDocs',
    'spectrumColoritems',
    'spectrumDocsTitle',
    'spectrumDocsText',
]);