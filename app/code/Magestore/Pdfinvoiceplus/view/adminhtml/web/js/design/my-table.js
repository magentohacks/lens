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
    'libDragtable',
    'Variable',
    'Magestore_Pdfinvoiceplus/js/action/re-calc-percent-table',
    'editContent',
    'colResizable'
], function ($, dragtable, Variable, reCalcPercentTableAction) {
    var MyTable = {
        'table': [],
        'columns': [],
        'totalWidthItem': 0,
        'canResize': true,
        'variableOption': {},
        'init': function (e, variableOption) {
            this.variableOption = variableOption;
            var that = this;
            if (typeof e == 'object') {
                this.table = e;
            } else {
                this.table = $('.' + e);
                if (this.table.length == 0) {
                    this.table = $('#' + e);
                }
            }
            if (this.table.length == 0) {
                return 0;
            }

            var is_swap_column = false; //check for column has swapped

            var reInit = function () {
                //init plugin
                $('#' + e).addClass('relative');
                $('#' + e).hover(function () {
                    $(this).addClass('border-table');
                }, function () {
                    $(this).removeClass('border-table');
                });

                dragtable.makeDraggable($('#' + e).get(0));
                //init colResize
                MyTable.initColResize(e);
                //init resize table
                MyTable.initResizable(e);
                //disable colResizeable
                $('#' + e).on('dragg_moving', function (event, dx, dy) {
                });

                is_swap_column = false;
                $('#' + e).on('column_swapped', function (event, from_index, to_index) {
                    is_swap_column = true;
                });

                $('#' + e).on('dragg_end_before', function (event, x, y) {
                    $('#' + e).colResizable({disable: true}); //disable resizeable columns
                });

                $('#' + e).on('dragg_end_after', function (event, x, y) {
                    if (is_swap_column) {
                        is_swap_column = false; //reset state to is not swap columns
                    }
                    MyTable.initColResize(e);
                    reCalcPercentTableAction('#' + e); //reset width table to percent
                    removeColHandle(); //replay remove col handle
                    addColHandle(); //re init
                });

                //reset width table to percent
                reCalcPercentTableAction('#' + e);
            };
            //add control ui
            var removeColHandle = function () {
                $('#' + e + '-CRC').addClass('control-ui');
                $('#' + e + '-CRC').children().append('<div class="' + e + '-remove-col-ui mytable-remove-col control-ui"></div>');//add remove icon handle
                $('#' + e + '-CRC').find('.' + e + '-remove-col-ui').click(function () {
                    $('#' + e).colResizable({disable: true});
                    var index = $(this).parent().index(); //get index
                    if ($('#' + e + ' tr').first().children().length > 1) {
                        $('#' + e).find('tr').each(function () {
                            $(this).children().eq(index).remove();
                        });
                    } else {
                        alert("Cannot remove this column.");
                    }
                    $(this).parent().remove();//remove the handle item
                    MyTable.columns = MyTable.table.children().not('.control-ui');
                    dragtable.makeDraggable($('#' + e).get(0));
                    MyTable.initColResize(e);

                    reCalcPercentTableAction('#' + e); //reset width table to percent

                    removeColHandle(); //replay this
                    addColHandle();

                    $('#' + e).trigger('removeColumn', [index + 1]); //send index of column removed
                });

            };
            //add control add new column
            var addColHandle = function () {
                var $handle = $('<div class="' + e + '-add-col-ui mytable-add-col control-ui" style="z-index: 1999"></div>');
                $('#' + e + '-CRC').append($handle);
                $handle.click(function () {
                    $('#' + e).colResizable({disable: true}); //disable col resizable
                    var length_before = $('#' + e + ' thead > tr').children().length || $('#' + e + ' tbody > tr').children().length;
                    //add cell row
                    var default_width = 10; //%
                    var newWidth = default_width;
                    //calc new cell width
                    newWidth = 100 / (length_before + 1);
                    if (newWidth > 0) {
                        $('#' + e + ' tr').each(function () {
                            var $new_cell = $(this).children().last().clone();
                            $new_cell.text('');//reset text clone
                            $new_cell.attr('placeholder', 'Click to edit!');
                            $(this).append($new_cell);
                            $new_cell.editContent(); //edit content cell
                        });
                    } else {
                        alert("Cannot remove this column.");
                    }
                    //add new width for new cell
                    $('#' + e + ' tr:first').children().last().width(newWidth + '%');

                    MyTable.columns = MyTable.table.children().not('.control-ui');
                    var length_after = $('#' + e + ' thead > tr').children().length || $('#' + e + ' tbody > tr').children().length;
                    //reload plugin run
                    dragtable.makeDraggable($('#' + e).get(0));
                    MyTable.initColResize(e);

                    reCalcPercentTableAction('#' + e); //reset width table to percent

                    removeColHandle();
                    addColHandle();
                    $('#' + e).trigger('column_added', [length_after, length_before]); //send to new columns index
                    Variable.init(MyTable.variableOption);
                });
            };

            reInit();
            removeColHandle();
            addColHandle();
        },
    
        'initColResize': function (e) {
            $('#' + e).colResizable({
                liveDrag: true,
                minWidth: 50,
                gripInnerHtml: '',
                onResize: function () {
                    reCalcPercentTableAction('#' + e); //reset width table to percent
                }
            });
        },
        'initResizable': function (e) { //init resize table
            $('#' + e).resizable({
                containment: "parent",
                handles: 'e',
                minWidth: 100,
                maxWidth: $('#' + e).parent().width(),
                create: function (event, ui) {
                    var hei = $(this).height();
                    //handle for resizeable
                    $(this).find('.ui-resizable-e').addClass('control-ui').css({
                        height: hei,
                        position: 'absolute',
                        top: 0,
                        right: 0,
                        zIndex: 999,
                        width: '10px',
                        cursor: 'e-resize'
                    });
                },
                resize: function (event, ui) {
                    $('#' + e + '-CRC').width($(this).width()); //fix sync control width
                },
                stop: function (event, ui) {
                    $(this).find('.ui-resizable-e').css("height", $(this).height());
                }
            });
        },
        'initRemove': function (target) {
            var that = this;
            $(target).click(function (e) {
                $(this).parent().remove();
                that.columns = that.table.children().not('.control-ui');
            });
        }
    };

    return MyTable;
});
