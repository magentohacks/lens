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
namespace Mageplaza\QuickbooksOnline\Observer\Creditmemo;

use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\QuickbooksOnline\Model\Source\QuickbooksModule;
use Mageplaza\QuickbooksOnline\Observer\AbstractQueue;

/**
 * Class CreditmemoSaveAfter
 * @package Mageplaza\QuickbooksOnline\Observer\Creditmemo
 */
class CreditmemoSaveAfter extends AbstractQueue
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
        $creditmemo = $observer->getEvent()->getDataObject();

        if ($creditmemo->getQuickbooksEntity() && !$creditmemo->hasQueueSave()) {
            $origData = $creditmemo->getOrigData();
            $this->helperSync->updateObject($origData, $creditmemo, QuickbooksModule::CREDIT_MEMO);
        } elseif (!$creditmemo->isObjectNew() && !$creditmemo->hasQueueSave()) {
            $this->helperSync->addObjectToQueue(QuickbooksModule::CREDIT_MEMO, $creditmemo);
        }
    }
}
