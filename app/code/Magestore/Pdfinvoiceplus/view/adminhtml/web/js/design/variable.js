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
    'Magestore_Pdfinvoiceplus/js/action/update-element-at-cursor',
    'Magestore_Pdfinvoiceplus/js/action/restore-selection',
    'Magestore_Pdfinvoiceplus/js/action/save-selection',
    'contextmenu'
], function(
    $,
    updateElementAtCursorAction,
    restoreSelectionAction,
    saveSelectionAction
) {
    /*manage insert variables*/
    var Variable = {
        'jsfocusEl': '',
        'focusEl': '',
        'selection': '',
        'contextType': '',
        'isLoadingVar': false,
        'isInserting': false,
        'print_type': '',
        'print_name': '',
        'menu_main_data': [],
        'menu_item_data': [],
        'variables': {},
        'menu_main_var': {},
        'menu_item_var': {},
        'type': {'order': '', 'invoice': '', 'creditmemo': '', 'shipment': '', 'quote': ''},
        'init': function(options) {
            this.print_type = options.print_type || '';
            this.orderVariableUrl = options.orderVariableUrl || '';
            this.invoiceVariableUrl = options.invoiceVariableUrl || '';
            this.creditmemoVariableUrl = options.creditmemoVariableUrl || '';
            this.shipmentVariableUrl = options.shipmentVariableUrl || '';
            this.quoteVariableUrl = options.quoteVariableUrl || '';
            this.formkey = options.formkey;

            $(document).on('focus', 'textarea', function(e) {
                Variable.focusEl = $(this);
                Variable.jsfocusEl = $(this).get(0);
            });
            //stop right click on edit area
            $(document).on('contextmenu', '#container-html', function(e) {
                e.stopPropagation();
                return false;
            });
            //stop right click on upload form
            $('.ajaxupload').on('contextmenu', 'form', function(e) {
                e.stopPropagation();
                return false;
            });
            //load allvariable
            this.loadVariable(this.print_type); //load all variable
            //listen click/focus element
            $(document).click(function(eIv) {
                if ($(eIv.target).closest('[contenteditable=true], .contenteditable').length) {
                    Variable.insertTo($(eIv.target).get(0)); //get focus element
                } else {
                    Variable.insertTo($());
                }
            });
        },
        'loadVariable': function(type) {
            var that = this;
            Variable.isLoadingVar = true;
            switch (type) {
                case 'order':
                    $.post(Variable.orderVariableUrl,
                        {form_key: Variable.formkey},
                        function(data) {
                            Variable.variables = data.order;
                            Variable.menu_customer = data.order.customer;
                            Variable.menu_printype = data.order.order;
                            Variable.menu_item = data.order.item;
                            Variable.isLoadingVar = false;
                            Variable.print_name = 'Order';
                            that.initMenu();
                        }, 'json');
                    break;
                case 'invoice':
                    $.post(Variable.invoiceVariableUrl,
                        {form_key: Variable.formkey},
                        function(data) {
                            Variable.variables = data.invoice;
                            Variable.menu_customer = data.invoice.customer;
                            Variable.menu_printype = data.invoice.invoice;
                            Variable.menu_item = data.invoice.item;
                            Variable.isLoadingVar = false;
                            Variable.print_name = 'Invoice';
                            that.initMenu();
                        }, 'json');
                    break;
                case 'creditmemo':
                    $.post(Variable.creditmemoVariableUrl,
                        {form_key: Variable.formkey},
                        function(data) {
                            Variable.menu_customer = data.creditmemo.customer;
                            Variable.menu_printype = data.creditmemo.creditmemo;
                            Variable.menu_item = data.creditmemo.item;
                            Variable.isLoadingVar = false;
                            Variable.print_name = 'Creditmemo';
                            that.initMenu();
                        }, 'json');
                    break;
                case 'shipment':
                    $.post(Variable.shipmentVariableUrl,
                        {form_key: Variable.formkey},
                        function(data) {
                            Variable.menu_customer = data.shipment.customer;
                            Variable.menu_printype = data.shipment.shipment;
                            Variable.menu_item = data.shipment.item;
                            Variable.isLoadingVar = false;
                            Variable.print_name = 'Shipment';
                            that.initMenu();
                        }, 'json');
                    break;
                case 'quote':
                    $.post(Variable.quoteVariableUrl,
                        {form_key: Variable.formkey},
                        function(data) {
                            Variable.menu_customer = data.quote.customer;
                            Variable.menu_printype = data.quote.quote;
                            Variable.menu_item = data.quote.item;
                            Variable.isLoadingVar = false;
                            Variable.print_name = 'Quote';
                            that.initMenu();
                        }, 'json');
                    break;
            }
        },
        insertVariable: function(value, target) {
            var textareaElm;// = this.jsfocusEl;
            if (target) {
                textareaElm = target;
            } else {
                textareaElm = this.jsfocusEl;
            }
            if (textareaElm) {
                var scrollPos = textareaElm.scrollTop;
                restoreSelectionAction(Variable.selection);
                updateElementAtCursorAction(textareaElm, value);
                textareaElm.focus();
                textareaElm.scrollTop = scrollPos;
                textareaElm = null;
            }
            this.jsfocusEl = null;
            return;
        },
        insertTo: function(e) {
            this.jsfocusEl = e;
        },
        initMenu: function() {
            var item = {};
            Variable.menu_main_data = []; //reset vars data
            Variable.menu_item_data = []; //reset vars data
            if (Variable.menu_customer.main === undefined)
                return;
            if (Variable.menu_item.main === undefined)
                return;
            //menu var main
            //customer group label
            var cus_group = {
                "Customer Variables:": {//label group
                    'onclick': function(menuItem, menu) {
                    },
                    'disabled': true,
                    'className': 'cus-name-group context-menu-name-group',
                    'hoverClassName': 'cus-name-group-hover context-menu-name-group-hover',
                    'title': 'Customer'
                }
            };
            Variable.menu_main_data.push(cus_group);
            //add items customer vars
            for (var i = 0; i < Variable.menu_customer.main.length; i++) {
                var label = Variable.menu_customer.main[i].label;
                var value = Variable.menu_customer.main[i].value;
                item = {};
                item[label] = {
                    'onclick': function(menuItem, menu, evt) {
                        Variable.insertVariable(menuItem.title);
                    },
                    'className': 'menu-item',
                    'hoverClassName': 'menu-item-hover',
                    'title': value
                };
                Variable.menu_main_data.push(item);
            }
            //add more btn customer vars
            Variable.menu_main_data.push({
                'more variables ...': {
                    'onclick': function(menuItem, menu, e) {
                        var items = pushMore(Variable.menu_customer.more);
                        for (var i = 0; i < items.length; i++) {
                            $(menuItem).before(items[i]);
                        }
                        $(menuItem).hide();
                        e.stopPropagation();
                    },
                    //disabled: true,
                    'className': 'context-menu-add-more',
                    'hoverClassName': 'context-menu-add-more-hover',
                    'title': 'insert more'
                }
            });
            //main var group label
            var main_group = {};
            main_group[Variable.print_name + ' Variables:'] = {//label group
                'onclick': function(menuItem, menu, e) {
                    e.stopPropagation();
                },
                'className': 'main-name-group context-menu-name-group',
                'hoverClassName': 'main-name-group-hover context-menu-name-group-hover',
                'title': 'Customer'
            };
            Variable.menu_main_data.push(main_group);
            //add items menu of printype vars
            for (var i = 0; i < Variable.menu_printype.main.length; i++) {
                var label = Variable.menu_printype.main[i].label;
                var value = Variable.menu_printype.main[i].value;
                item = {};
                item[label] = {
                    'onclick': function(menuItem, menu) {
                        Variable.insertVariable(menuItem.title);
                    },
                    'className': 'menu-item',
                    'hoverClassName': 'menu-item-hover',
                    'title': value
                };
                Variable.menu_main_data.push(item);
            }
            //add more btn of menu printype vars
            Variable.menu_main_data.push({
                'more variables ...': {
                    'onclick': function(menuItem, menu, e) {
                        var items = pushMore(Variable.menu_printype.more);
                        for (var i = 0; i < items.length; i++) {
                            $(menuItem).before(items[i]);
                        }
                        $(menuItem).hide();
                        e.stopPropagation();
                    },
                    'className': 'context-menu-add-more',
                    'hoverClassName': 'context-menu-add-more-hover',
                    'title': 'insert more'
                }
            });
            //build main menu
            $("[contextmenu-type=main]").contextMenu(Variable.menu_main_data,
                {
                    'showSpeed': 0, 'hideSpeed': 0,
                    showCallback: function() {
                        Variable.selection = saveSelectionAction();
                        Variable.isInserting = true;
                    },
                    hideCallback: function() {
                        $('.context-menu-item-more').remove();
                        Variable.isInserting = false;
                    },
                    beforeShow: function() {
                        //check has focus
                        if (!Variable.jsfocusEl) {
                            return false;
                        }
                        //hide menu
                        $('.context-menu').closest('table').hide();
                        //hide contextmenu shadow
                        $('div.context-menu-shadow').hide();
                        $('.context-menu-add-more').show();
                        $('.' + this.className).disableSelection();
                    }
                }
            );
            //######################################
            //menu var item group label
            var item_group = {};
            item_group[Variable.print_name + ' Items Variables:'] = {//label group
                onclick: function(menuItem, menu, e) {
                    e.stopPropagation();
                },
                disabled: true,
                className: 'item-name-group context-menu-name-group',
                hoverClassName: 'item-name-group-hover context-menu-name-group-hover',
                title: 'Items variables'
            };
            Variable.menu_item_data.push(item_group);
            //menu var item
            for (var i = 0; i < Variable.menu_item.main.length; i++) {
                var label = Variable.menu_item.main[i].label;
                var value = Variable.menu_item.main[i].value;
                item = {};
                item[label] = {onclick: function(menuItem, menu) {
                    Variable.insertVariable(menuItem.title);
                },
                    'className': 'menu-item',
                    'hoverClassName': 'menu-item-hover',
                    'title': value
                };
                Variable.menu_item_data.push(item);
            }
            //add more btn of menu items vars
            Variable.menu_item_data.push({
                'more variables ...': {
                    'onclick': function(menuItem, menu, e) {
                        var items = pushMore(Variable.menu_item.more);
                        for (var i = 0; i < items.length; i++) {
                            $(menuItem).before(items[i]);
                        }
                        $(menuItem).hide();
                        e.stopPropagation();
                    },
                    'className': 'context-menu-add-more',
                    'hoverClassName': 'context-menu-add-more-hover',
                    'title': 'insert more'
                }
            });
            $("[contextmenu-type=item]").contextMenu(Variable.menu_item_data,
                {
                    'showSpeed': 0, 'hideSpeed': 0,
                    showCallback: function() {
                        Variable.selection = saveSelectionAction();
                        Variable.isInserting = true;
                    },
                    hideCallback: function() {
                        $('.context-menu-item-more').remove();
                        $('.context-menu-add-more').hide();
                        Variable.isInserting = false;
                    },
                    beforeShow: function() {
                        //check has focus
                        if (!Variable.jsfocusEl) {
                            return false;
                        }
                        //hide menu
                        $('.context-menu').closest('table').hide();
                        //hide contextmenu shadow
                        $('div.context-menu-shadow').hide();
                        $('.context-menu-add-more').show();
                        $('.' + this.className).disableSelection();
                    }
                }
            );

            //function helper
            var getMoreButton = function(more) {
                var $more_item = $('<div>', {
                    'title': "insert more",
                    'class': "context-menu-add-more"
                }).text('more variable ...')
                    .click(function(event) {
                        var items = pushMore(more);
                        for (var i = 0; i < items.length; i++) {
                            $(this).before(items[i]);
                        }
                        $(this).remove();
                        event.stopPropagation();
                    }).hover(function() {
                        $(this).addClass('context-menu-add-more-hover');
                    }, function() {
                        $(this).removeClass('context-menu-add-more-hover');
                    });
                return $more_item;
            };

            var pushMore = function(more) {
                var item = [];
                for (var i = 0; i < more.length; i++) {
                    var value = more[i].value;
                    var $jItem = $('<div>', {'title': value, 'class': 'context-menu-item context-menu-item-more menu-item'});
                    $jItem.append($('<div>', {'class': 'context-menu-item-inner'}).text(more[i].label));
                    $jItem.hover(function() {
                        $(this).addClass('menu-item-hover');
                    }, function() {
                        $(this).removeClass('menu-item-hover');
                    });
                    $jItem.click(function() {
                        Variable.insertVariable(this.title);
                    });
                    item.push($jItem);
                }
                return item;
            };

        }
    };

    return Variable;
});
