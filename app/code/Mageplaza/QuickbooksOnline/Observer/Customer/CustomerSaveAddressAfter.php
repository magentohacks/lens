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

use Psr\Log\LoggerInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Address;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\QuickbooksOnline\Helper\Data as HelperData;
use Mageplaza\QuickbooksOnline\Helper\Sync as HelperSync;
use Mageplaza\QuickbooksOnline\Model\QueueFactory;
use Mageplaza\QuickbooksOnline\Model\Source\QuickbooksModule;
use Mageplaza\QuickbooksOnline\Observer\AbstractQueue;
use Mageplaza\QuickbooksOnline\Helper\Mapping;


/**
 * Class CustomerSaveAddressAfter
 * @package Mageplaza\QuickbooksOnline\Observer\Customer
 */
class CustomerSaveAddressAfter extends AbstractQueue
{
    /**
     * @var AbstractData
     */
    protected $abstractData;

    /**
     * @var Mapping
     */
    protected $mappingHelper;

    /**
     * AbstractModelSaveBefore constructor.
     *
     * @param QueueFactory $queueFactory
     * @param HelperSync $helperSync
     * @param HelperData $helperData
     * @param LoggerInterface $logger
     * @param AbstractData $abstractData
     * @param Mapping $mappingHelper
     */
    public function __construct(
        QueueFactory $queueFactory,
        HelperSync $helperSync,
        HelperData $helperData,
        LoggerInterface $logger,
        AbstractData $abstractData,
        Mapping $mappingHelper
    ) {
        $this->abstractData  = $abstractData;
        $this->mappingHelper = $mappingHelper;

        parent::__construct($queueFactory, $helperSync, $helperData, $logger);
    }

    /**
     * @param Observer $observer
     *
     * @return AbstractQueue|void
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function executeAction(Observer $observer)
    {
        $customerAddress = $observer->getEvent()->getDataObject();
        $origAddress     = $customerAddress->getOrigData();
        $currentAddress  = $customerAddress->getData();
        $customerData    = $customerAddress->getCustomer();
        $origData        = $customerData->getOrigData();

        if ($customerData->getQuickbooksEntity()) {
            if (!$origAddress) {
                if ($customerAddress->getDefaultShipping() || $customerAddress->getDefaultBilling()) {
                    $this->helperSync->updateObject($origData, $customerData, QuickbooksModule::CUSTOMER, true);
                }
            } else {
                $origMappingAddress    = $this->getAddressData($origAddress, $customerData);
                $currentMappingAddress = $this->getAddressData($currentAddress, $customerData);

                if ($origMappingAddress !== $currentMappingAddress || $this->isChangeBillShip($customerAddress, $customerData)) {
                    $this->helperSync->updateObject($origData, $customerData, QuickbooksModule::CUSTOMER, true);
                }
            }
        }
    }

    /**
     * @param Address $customerAddress
     * @param Customer $customerData
     *
     * @return bool
     */
    public function isChangeBillShip($customerAddress, $customerData)
    {
        $addressId            = $customerAddress->getEntityId();;
        $currentBillAddressId = $customerAddress->getDefaultBilling();
        $currentShipAddressId = $customerAddress->getDefaultShipping();
        $defaultBillAddressId = $customerData->getDefaultBilling();
        $defaultShipAddressId = $customerData->getDefaultShipping();

        if ($addressId == $defaultBillAddressId && $addressId != $defaultShipAddressId) {
            if (!$currentBillAddressId || $currentShipAddressId) {
                return true;
            }
        }
        if ($addressId != $defaultBillAddressId && $addressId == $defaultShipAddressId) {
            if (!$currentShipAddressId || $currentBillAddressId) {
                return true;
            }
        }

        if ($addressId == $defaultBillAddressId && $addressId == $defaultShipAddressId) {
            if (!$currentShipAddressId || !$currentBillAddressId) {
                return true;
            }
        }

        if ($addressId != $defaultBillAddressId && $addressId != $defaultShipAddressId) {
            if ($currentBillAddressId || $currentShipAddressId) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $addressData
     * @param Customer $customerData
     *
     * @return array
     */
    public function getAddressData($addressData, $customerData)
    {
        $sync    = $this->helperSync->getSyncRule($customerData, QuickbooksModule::CUSTOMER);
        $mapping = $this->abstractData->jsonDecode($sync->getMapping());
        $addressMapping = [];

        foreach ($mapping as $mappingField) {
            if ($mappingField['value']) {
                $data = $this->mappingHelper->matchData($mappingField['value']);
                foreach ($data as $field) {
                    if ($shippingField = $this->helperSync->checkAddressField($field, 'shipping_')) {
                        $addressMapping['shipping'][$shippingField] = $addressData[$shippingField] ?: $shippingField;
                    }

                    if ($billingField = $this->helperSync->checkAddressField($field, 'billing_')) {
                        $addressMapping['billing'][$billingField] = $addressData[$billingField] ?: $billingField;
                    }
                }
            }
        }

        return $addressMapping;
    }
}
