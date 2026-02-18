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
    return function (evt) {
        var range, x = evt.clientX, y = evt.clientY;
        // Try the simple IE way first
        if (document.body.createTextRange) {
            range = document.body.createTextRange();
            if (range.moveToPoint) {
                range.moveToPoint(x, y);
            }
        }
        else if (typeof document.createRange != "undefined") {
            // Try Mozilla's rangeOffset and rangeParent properties, which are exactly what we want
            if (typeof evt.rangeParent != "undefined") {
                range = document.createRange();
                range.setStart(evt.rangeParent, evt.rangeOffset);
                range.collapse(true);
            }
            // Try the standards-based way next
            else if (document.caretPositionFromPoint) {
                var pos = document.caretPositionFromPoint(x, y);
                range = document.createRange();
                range.setStart(pos.offsetNode, pos.offset);
                range.collapse(true);
            }
            // Next, the WebKit way
            else if (document.caretRangeFromPoint) {
                range = document.caretRangeFromPoint(x, y);
            }
        }

        return range;
    }
});