<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_QuickbooksOnline
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\QuickbooksOnline\Observer\Order;

use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\QuickbooksOnline\Model\Queue;
use Mageplaza\QuickbooksOnline\Model\Source\MagentoObject;
use Mageplaza\QuickbooksOnline\Model\Source\QuickbooksModule;
use Mageplaza\QuickbooksOnline\Observer\AbstractQueue;

/**
 * Class OrderCommitDelete
 * @package Mageplaza\QuickbooksOnline\Observer\Order
 */
class OrderCommitDelete extends AbstractQueue
{
    /**
     * @param Observer $observer
     *
     * @return AbstractQueue|void
     * @throws NoSuchEntityException
     */
    public function executeAction(Observer $observer)
    {
        $order = $observer->getEvent()->getDataObject();
        /**
         * @var Queue $queue
         */
        $queue             = $this->queueFactory->create();
        $invoiceCollection = $order->getInvoiceCollectionBeforeDelete();

        if ($invoiceCollection) {
            foreach ($invoiceCollection as $invoice) {
                $queue->addDeleteObjectToQueue($invoice, MagentoObject::INVOICE);
            }
        }

        $creditmemoCollection = $order->getCreditmemoCollectionBeforeDelete();

        if ($creditmemoCollection) {
            foreach ($creditmemoCollection as $creditmemo) {
                $queue->addDeleteObjectToQueue($creditmemo, MagentoObject::CREDIT_MEMO);
            }
        }

        $queue->addDeleteObjectToQueue($order, QuickbooksModule::ORDER);
    }
}
