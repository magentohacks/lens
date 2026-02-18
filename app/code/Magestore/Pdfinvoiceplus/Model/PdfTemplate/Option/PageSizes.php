<?php

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

namespace Magestore\Pdfinvoiceplus\Model\PdfTemplate\Option;

    /**
     * class PageSizes
     *
     * @category Magestore
     * @package  Magestore_Pdfinvoiceplus
     * @module   Pdfinvoiceplus
     * @author   Magestore Developer
     */
/**
 * Class PageSizes
 * @package Magestore\Pdfinvoiceplus\Model\PdfTemplate\Option
 */
class PageSizes implements \Magento\Framework\Option\ArrayInterface
{
    /**
     *
     */
    const PAGE_SIZE_LETTER = 'Letter';
    /**
     *
     */
    const PAGE_SIZE_A4 = 'A4';
    /**
     *
     */
    const PAGE_SIZE_A5 = 'A5';

    /**
     * get available page sizes.
     *
     * @return []
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::PAGE_SIZE_LETTER, 'label' => __('Letter')],
            ['value' => self::PAGE_SIZE_A4, 'label' => __('A4')],
            ['value' => self::PAGE_SIZE_A5, 'label' => __('A5')],
        ];
    }
}