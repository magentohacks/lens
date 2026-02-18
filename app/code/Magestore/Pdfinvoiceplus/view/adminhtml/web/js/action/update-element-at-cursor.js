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
], function ($) {

    return function (el, value) {
        if (
            $(el).prop("tagName") != 'INPUT' //not input
            && $(el).prop("tagName") != 'TEXTAREA' //not textarea
            && $(el).closest('[contenteditable=true]').length //must is contenteditable true
        ) {
            var sel = window.getSelection();
            var text = value;
            if (sel.rangeCount > 0) {
                var range = sel.getRangeAt(0);
                var startNode = range.startContainer, startOffset = range.startOffset;
                var boundaryRange = range.cloneRange();
                var textNode = document.createTextNode(text);
                boundaryRange.deleteContents();
                boundaryRange.setStart(startNode, startOffset);
                boundaryRange.collapse(true);
                boundaryRange.insertNode(textNode);
                // Reselect the original text
                range.setStartBefore(textNode);
                range.setEndAfter(textNode);
                sel.removeAllRanges();
                sel.addRange(range);
            }
        } else if (el.selectionStart || el.selectionStart == '0') {
            var startPos = el.selectionStart;
            var endPos = el.selectionEnd;
            el.value = el.value.substring(0, startPos) + value + el.value.substring(endPos, el.value.length);
        } else {
            el.value += value;
        }
        el.selectionStart = el.selectionEnd += value.length;
    }
});