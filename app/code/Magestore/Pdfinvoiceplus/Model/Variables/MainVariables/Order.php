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
use Magestore\Pdfinvoiceplus\Model\Variables\AbstractVariableOption;

/**
 * class Order
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Order extends \Magestore\Pdfinvoiceplus\Model\Variables\AbstractMainVariables
{
    /**
     * @var array
     */
    protected $_mainVariables = [
        "increment_id",
        "status",
        "created_at",
        "discount_amount",
        "tax_amount",
        "grand_total",
        "total_paid",
        "total_qty_ordered",
        "total_due",
        "billing_address",
        "shipping_address",
        "payment_method",
        "shipping_method"
    ];

    /**
     * Get prefix variable
     *
     * @return string
     */
    public function getPrefixVariable()
    {
        return AbstractVariableOption::PREFIX_VAR_ORDER;
    }

    /**
     * get config variables option
     *
     * @return array
     */
    public function getConfigVariables()
    {
        return $this->_optionManager->get(OptionManager::OPTION_VARIABLE_CONFIG_ORDER)->toOptionArray();
    }
}