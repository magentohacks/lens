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
namespace Mageplaza\QuickbooksOnline\Observer\Customer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\QuickbooksOnline\Model\Source\QuickbooksModule;
use Mageplaza\QuickbooksOnline\Observer\AbstractQueue;

/**
 * Class CustomerDeleteAddress
 * @package Mageplaza\QuickbooksOnline\Observer\Customer
 */
class CustomerDeleteAddress extends AbstractQueue
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
        $customerAddress      = $observer->getEvent()->getDataObject();
        $customerData         = $customerAddress->getCustomer();
        $origData             = $customerData->getOrigData();
        $addressId            = $customerAddress->getEntityId();
        $defaultBillAddressId = $customerData->getDefaultBilling();
        $defaultShipAddressId = $customerData->getDefaultShipping();

        if ($customerData->getQuickbooksEntity()) {
            if ($addressId === $defaultBillAddressId || $addressId === $defaultShipAddressId || !$defaultBillAddressId || !$defaultShipAddressId) {
                $this->helperSync->updateObject($origData, $customerData, QuickbooksModule::CUSTOMER, true);
            }
        }
    }
}
