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

namespace Magestore\Pdfinvoiceplus\Controller\Adminhtml\Design\Variable;

use Magestore\Pdfinvoiceplus\Model\MainVariablesManager;

/**
 * class Order
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Order extends \Magestore\Pdfinvoiceplus\Controller\Adminhtml\Design\AbstractVariable
{
    /**
     * @return array
     */
    public function getMainVariablesData()
    {
        return [
            'order' => [
                'customer' => $this->_mainVariablesManager->get(MainVariablesManager::MAIN_VARIABLE_CUSTOMER)->getVariables(),
                'order'    => $this->_mainVariablesManager->get(MainVariablesManager::MAIN_VARIABLE_ORDER)->getVariables(),
                'item'     => $this->_mainVariablesManager->get(MainVariablesManager::MAIN_VARIABLE_ORDER_ITEM)->getVariables(),
            ],
        ];
    }
}