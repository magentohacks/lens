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
    'MyHtml',
    'mage/translate',
    'jquery/tinyMce',
    'jquery/ui',
], function($, MyHtml, $t) {
    "use strict";

    $.widget('magestore.menuWrapper', {
        options: {
            saveHtmlUrl: '',
            editHtmlUrl: '',
            backUrl: '',
            saveData: {},
            syncInfoUpdateUrl: '',
            syncInfoResetUrl: '',
            changeBackgroundUrl: '',
            indicatorImageUrl: '',
            advanceEdtior: false,
            script_url: ''

        },

        _create: function () {
            var self = this, options = this.options;

            this._initHtmlAction();

            $(self.element).find(".changebackground").editable(options.changeBackgroundUrl, {
                indicator: '<img src="'+ options.indicatorImageUrl + '" />',
                type: 'ajaxupload',
                submit: 'Upload',
                cancel: 'Cancel',
                tooltip: "Click to upload...",
                name: "change-background",
                id: 'change-background',
                placeholder: '',
                imageUploadUrl: options.changeBackgroundUrl
            });

        },

        initAdvanceEditor: function () {
            var self = this, options = this.options;
            $('#edit_html').tinymce({
                // Location of TinyMCE script
                script_url: options.script_url,
                // General options
                theme: "advanced",
                width: "100%",
                height: 1000,
                relative_urls: false,
                valid_children: "+body[style]",
                valid_elements: "*[*]",
                plugins: "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
                // Theme options
                theme_advanced_buttons1: "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
                theme_advanced_buttons2: "bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor,fullscreen",
                theme_advanced_buttons3: "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
                theme_advanced_toolbar_location: "top",
                theme_advanced_toolbar_align: "left",
                theme_advanced_statusbar_location: "bottom",
                theme_advanced_resizing: true,
                // Example content CSS (should be your site CSS)
                content_css: "css/content.css",
                // Drop lists for link/image/media/template dialogs
                template_external_list_url: "lists/template_list.js",
                external_link_list_url: "lists/link_list.js",
                external_image_list_url: "lists/image_list.js",
                media_external_list_url: "lists/media_list.js",
                // Replace values for the template plugin
                template_replace_values: {
                    username: "Some User",
                    staffid: "991234"
                }
            });

            MyHtml.toHtml = function () {
                return tinyMCE.get('edit_html').getContent();
            }

        },

        _initHtmlAction: function () {
            var self = this, options = this.options;

            MyHtml.init(options);

            if(options.advanceEdtior) {
                this.initAdvanceEditor();
            }

            if(options.backUrl) {
                $(self.element).find('.back-action').click(function () {
                    window.location.href = options.backUrl;
                });
            }

            $(self.element).find('.save-action').click(function () {
                MyHtml.save();
            });

            if(options.editHtmlUrl){
                $(self.element).find('.edit-action').click(function () {
                    MyHtml.save(options.editHtmlUrl);
                });
            }

            $(self.element).find('.load-action').click(function () {
                if (confirm($t('If you reset this template, it will get back to the default setting. Are you sure?'))) {
                    MyHtml.resetDefault();
                }
            });
        }
    });

    return $.magestore.menuWrapper;
});
