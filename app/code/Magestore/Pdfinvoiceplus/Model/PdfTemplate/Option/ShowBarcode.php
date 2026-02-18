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
 * class ShowBarcode
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class ShowBarcode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     *
     */
    const STATUS_YES = 1;
    /**
     *
     */
    const STATUS_NO = 2;

    /**
     * get available statuses.
     *
     * @return []
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::STATUS_YES, 'label' => __('Yes')],
            ['value' => self::STATUS_NO, 'label' => __('No')],
        ];
    }
}