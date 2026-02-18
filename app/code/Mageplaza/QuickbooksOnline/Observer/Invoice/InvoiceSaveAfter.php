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
 * @category  Mageplaza
 * @package   Mageplaza_QuickbooksOnline
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\QuickbooksOnline\Observer\Invoice;

use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\QuickbooksOnline\Model\Source\QuickbooksModule;
use Mageplaza\QuickbooksOnline\Observer\AbstractQueue;

/**
 * Class InvoiceSaveAfter
 * @package Mageplaza\QuickbooksOnline\Observer\Invoice
 */
class InvoiceSaveAfter extends AbstractQueue
{
    /**
     * @param Observer $observer
     *
     * @return AbstractQueue|void
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function executeAction(Observer $observer)
    {
        $invoice = $observer->getEvent()->getDataObject();

        if ($invoice->getQuickbooksEntity() && !$invoice->hasQueueSave()) {
            $origData = $invoice->getOrigData();
            $this->helperSync->updateObject($origData, $invoice, QuickbooksModule::INVOICE);
        } elseif (!$invoice->isObjectNew() && !$invoice->hasQueueSave()) {
            $this->helperSync->addObjectToQueue(QuickbooksModule::INVOICE, $invoice);
        }
    }
}
