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

namespace Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder;

/**
 * class TableItem
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class TableItem extends \Magestore\Pdfinvoiceplus\Block\Adminhtml\AbstractTemplateInformation
{
    const COLUMN_PRODUCT = '{{var items_name}}<br/>{{var items_product_options}}';
    const COLUMN_SKU = '{{var items_sku}}';
    const COLUMN_PRICE = '{{var items_price}}';
    const COLUMN_QTY = '{{var items_qty}}';
    const COLUMN_Tax = '{{var items_tax_amount}}';
    const COLUMN_SUBTOTAL = '{{var items_row_total}}';

    protected $_template = 'Magestore_Pdfinvoiceplus::default-template/table-item.phtml';

    /**
     * @return array
     */
    public function getItemMap()
    {
        return [
            static::COLUMN_PRODUCT  => strtoupper($this->translate('Product')),
            static::COLUMN_SKU      => strtoupper($this->translate('Sku')),
            static::COLUMN_PRICE    => strtoupper($this->translate('Price')),
            static::COLUMN_QTY      => strtoupper($this->translate('Qty')),
            static::COLUMN_Tax      => strtoupper($this->translate('Tax')),
            static::COLUMN_SUBTOTAL => strtoupper($this->translate('Subtotal')),
        ];
    }
}