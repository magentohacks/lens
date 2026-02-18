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
    'mage/translate',
    'Magento_Ui/js/modal/modal'
], function ($, $t) {
    "use strict";

    window.QuickbooksVariables = {
        variablesContent: null,
        dialogWindowId: 'variables-chooser',
        editor: null,

        /**
         * Set editor
         *
         * @param editor
         */
        setEditor: function (editor) {
            this.editor = editor;
        },

        /**
         * Open variable chooser
         *
         * @param variables
         */
        openVariableChooser: function (variables) {
            variables = [variables];
            if (this.variablesContent === null && variables) {
                this.variablesContent = '<ul class="insert-variable">';
                variables.each(function (variableGroup) {
                    if (variableGroup.label && variableGroup.value) {
                        this.variablesContent += '<li><b>' + variableGroup.label + '</b></li>';
                        (variableGroup.value).each(function (variable) {
                            if (variable.value && variable.label) {
                                this.variablesContent += '<li>' +
                                    this.prepareVariableRow(variable.value, variable.label) + '</li>';
                            }
                        }.bind(this));
                    }
                }.bind(this));

                this.variablesContent += '</ul>';
            }

            if (this.variablesContent) {
                this.openDialogWindow(this.variablesContent);
            }
        },

        /**
         * Open popup
         *
         * @param variablesContent
         */
        openDialogWindow: function (variablesContent) {
            var windowId = this.dialogWindowId;

            $('<div id="' + windowId + '">' + variablesContent + '</div>').modal({
                title: $t('Insert Variable...'),
                type: 'slide',
                buttons: [],
                closed: function (e, modal) {
                    modal.modal.remove();
                }
            });

            $('#' + windowId).modal('openModal');
        },

        /**
         * Close popup
         */
        closeDialogWindow: function () {
            $('#' + this.dialogWindowId).modal('closeModal');
        },

        /**
         * Prepare variable row
         *
         * @param   varValue
         * @param   varLabel
         * @returns {string}
         */
        prepareVariableRow: function (varValue, varLabel) {
            return '<a href="#" onclick="' + this.insertFunction(varValue) + '">' + varLabel + '</a>';
        },

        /**
         * @param value
         * @returns {string}
         */
        insertFunction: function (value) {
            return 'QuickbooksVariables.insertVariable(&apos;' + value + '&apos;)';
        },

        /**
         * Insert variable to editor at cursor
         *
         * @param value
         */
        insertVariable: function (value) {
            var editorElement = $('#' + this.editor);
            var oldValue      = editorElement.val();

            editorElement.val(oldValue + value);
            $('#' + this.dialogWindowId).modal('closeModal');

        }
    };

    return window.QuickbooksVariables;
});
