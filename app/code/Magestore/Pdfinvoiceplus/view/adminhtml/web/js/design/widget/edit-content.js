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
    'Variable',
    'Magestore_Pdfinvoiceplus/js/action/get-mouse-event-caret-range',
    'Magestore_Pdfinvoiceplus/js/action/select-range',
    'jquery/ui',
], function($, Variable, getMouseEventCaretRangeAction, selectRangeAction) {
    "use strict";
    $.widget('magestore.editContent', {
        options: {
        },

        _create: function() {
            this.element.each(function(index, el) {
                var self = this;
                $(self).attr('placeholder', 'Click to edit!'); //add place holder
                if (!$(self).hasClass('contenteditable')) {
                    $(self).addClass('contenteditable');
                }
                $(self).on('click', function(target) {
                    if ($(this).find('.editContentTable').length <= 0) {
                        var $editbox = $('<div class="editContentTable" contenteditable="true" style=""></div>');
                        var oldText = $(self).html();
                        $editbox.css('min-height', $(self).height()).html(oldText);
                        $(self).html('');
                        $(self).attr('contenteditable', false).append($editbox);
                        $($editbox).trigger('focus');
                        var caretRange = getMouseEventCaretRangeAction(target);
                        window.setTimeout(function() {
                            selectRangeAction(caretRange);
                        }, 10);
                        $(document).click(editContentSubmit);
                        $($editbox).focusout(function() {
                            console.log(Variable.isInserting);
                            if (!Variable.isInserting) {
                                editContentSubmit();
                            }
                        });
                        //enable selection for edit
                        $(this).enableSelection();
                        $($editbox).focus(function() {
                            $('#tooltip').remove(); //remove tooltip
                        });

                        //send to insert variable
                        Variable.insertTo($editbox.get(0)); //can insert variable
                        target.stopPropagation();
                    }else{
                        //send to insert variable
                        Variable.insertTo($(this).find('.editContentTable').get(0)); //can insert variable
                    }

                    $(this).find('.editContentTable').click(function(event) {
                        event.stopPropagation();
                    }).bind('keypress', function(e) {
                        var code = (e.keyCode ? e.keyCode : e.which);
                        if (code == 13) {
                            //editContentSubmit();
                        }
                    });
                    //hide menu
                    $('.context-menu').closest('table').hide();
                    //hide contextmenu shadow
                    $('div.context-menu-shadow').hide();

                    //target.stopPropagation();
                });

                var editContentSubmit = function() {
                    $('.contenteditable').attr('contenteditable', true)
                    var editing = $('.editContentTable');
                    editing.each(function() {
                        if (!$(this).text()) {
                            $(this).parent().html('');
                        } else {
                            $(this).parent().html($(this).html());
                        }
                        $(this).unbind('click');
                    });

                    $(document).unbind('click', editContentSubmit);
                };
            });

        },
    });

    return $.magestore.editContent;
});