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

namespace Magestore\Pdfinvoiceplus\Model\Variables\MainVariables;

use Magestore\Pdfinvoiceplus\Model\OptionManager;

/**
 * class OrderItem
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class OrderItem extends \Magestore\Pdfinvoiceplus\Model\Variables\AbstractMainVariablesItem
{
    /**
     * @var array
     */
    protected $_mainVariables = [
        "sku",
        "name",
        "discount_amount",
        "row_total",
        "row_total_incl_tax",
        "qty_ordered",
        "qty_invoiced",
        "qty_refunded"
    ];

    /**
     * get config variables option
     *
     * @return array
     */
    public function getConfigVariables()
    {
        return $this->_optionManager->get(OptionManager::OPTION_VARIABLE_CONFIG_ORDER_ITEM)->toOptionArray();
    }
}