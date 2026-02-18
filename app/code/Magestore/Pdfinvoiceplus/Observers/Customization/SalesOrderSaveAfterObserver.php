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
 * @package     Magestore_Membership
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Pdfinvoiceplus\Observers\Customization;

use Magento\Framework\Event\ObserverInterface;

/**
 * class OrderSaveAfterObserver
 *
 * @category Magestore
 * @package  Magestore_Membership
 * @module   Membership
 * @author   Magestore Developer
 */
class SalesOrderSaveAfterObserver implements ObserverInterface
{

    /**
     * order closed status
     * @string
     */
    const ORDER_STATUS_CLOSED = 'closed';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magestore\Pdfinvoiceplus\Model\SystemConfig
     */
    protected $_configs;


    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\Pdfinvoiceplus\Model\SystemConfig $systemConfig
    )
    {
        $this->_objectManager = $objectManager;
        $this->_configs = $systemConfig;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_configs->isEnablePdfInvoicePlus()) {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $observer->getEvent()->getOrder();

            switch ($order->getStatus()) {
                case $this->_configs->getAutoSendInvoiceStatus():
                    // when order status turns into processing
                    $invoice = $order->getInvoiceCollection()->getLastItem();
                    if ($invoice->getEntityId()) {
                        $this->_objectManager->create('Magento\Sales\Api\InvoiceManagementInterface')
                            ->notify($invoice->getEntityId());
                    }
                    break;

                case self::ORDER_STATUS_CLOSED:
                    // when order status turns into closed
                    $creditmemo = $order->getCreditmemosCollection()->getLastItem();
                    if ($creditmemo->getEntityId()) {
                        $this->_objectManager->create('Magento\Sales\Api\CreditmemoManagementInterface')
                            ->notify($creditmemo->getEntityId());
                    }
                    break;
            }
        }
    }
}