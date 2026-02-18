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

namespace Magestore\Pdfinvoiceplus\Controller\Adminhtml\InsertVariable;

use Magestore\Pdfinvoiceplus\Model\OptionManager;

/**
 * class Order
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Order extends \Magestore\Pdfinvoiceplus\Controller\Adminhtml\InsertVariable
{
    /**
     * @return array
     */
    public function getBarcodeFilenameVariables()
    {
        return [
            'label' => __('Order'),
            'value' => $this->_optionManager->get(OptionManager::OPTION_VARIABLE_BARCODEFILENAME_ORDER)->toOptionArray(),
        ];
    }
}