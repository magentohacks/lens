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

namespace Magestore\Pdfinvoiceplus\Controller\PrintPdf;

/**
 * class Invoice
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Invoice extends \Magestore\Pdfinvoiceplus\Controller\PrintPdf\AbstractPrintPdf
{
    /**
     * @return \Magento\Sales\Model\Order\Invoice
     */
    public function getRenderingEntity()
    {
        $invoiceId = $this->getRequest()->getParam('invoice_id');

        return $this->_objectManager->create('Magento\Sales\Model\Order\Invoice')->load($invoiceId);
    }
}