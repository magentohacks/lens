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
namespace Mageplaza\QuickbooksOnline\Block\Adminhtml\Sync\Edit\Tab\Report;

use Magento\Backend\Block\Template\Context;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Mageplaza\QuickbooksOnline\Block\Adminhtml\Sync\Edit\Tab\QueueReport;
use Mageplaza\QuickbooksOnline\Model\ResourceModel\Queue\CollectionFactory as QueueCollection;
use Mageplaza\QuickbooksOnline\Model\Source\MagentoObject;

/**
 * Class Customer
 * @package Mageplaza\QuickbooksOnline\Block\Adminhtml\Sync\Edit\Tab\Report
 */
class Customer extends QueueReport
{
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * Customer constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param QueueCollection $queueCollection
     * @param CustomerFactory $customerFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        QueueCollection $queueCollection,
        CustomerFactory $customerFactory,
        array $data = []
    ) {
        $this->customerFactory = $customerFactory;
        parent::__construct($context, $registry, $formFactory, $queueCollection, $data);
    }

    /**
     * @return Phrase|string
     */
    public function getTabLabel()
    {
        return __('Quickbooks Online');
    }

    /**
     * @param Fieldset $fieldset
     */
    public function addExtraFields($fieldset)
    {
        $id       = $this->getRequest()->getParam('id');
        $customer = $this->customerFactory->create()->load($id);
        $this->getRequest()->setParam('magento_object', MagentoObject::CUSTOMER);
        $this->addQuickbooksEntity($fieldset, $customer);
    }

    /**
     * @return bool|mixed
     */
    public function canShowTab()
    {
        return $this->getRequest()->getParam('id');
    }
}
