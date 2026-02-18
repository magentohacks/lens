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
], function($) {

    function synGripTable(e) {
        var b = parseInt($(e).css('border-spacing')) || 2;
        var gc = $(e + '-CRC');
        var t = $(e), headerOnly = false;
        gc.width(t.width());
        for (var i = 0; i < t.find('tr:first').children().length; i++) {
            var c = t.find('tr:first').children().eq(i);
            gc.children().eq(i).css({
                left: c.offset().left - t.offset().left + c.outerWidth() + b / 2 + 'px',
                height: headerOnly ? t.find('tr:first').children().eq(0).outerHeight() : t.outerHeight()
            });
        }
    }

    return function(select) {
        var $table = $(select);
        var wid = 0;
        var $headCell;
        if ($table.find('tr:first th').length) {
            $headCell = $table.find('tr:first th');
        } else {
            $headCell = $table.find('tr:first td');
        }
        //calc total width
        $headCell.each(function() {
            var _this = $(this);
            wid += parseInt(_this.outerWidth());
        });
        //reset cell width
        $headCell.each(function() {
            var _this = $(this);
            _this.width(parseInt(_this.outerWidth()) / wid * 100 + "%");
        });
        //reset cell controll width
        synGripTable(select);
    }
});