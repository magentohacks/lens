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
namespace Mageplaza\QuickbooksOnline\Model;

use Magento\Backend\Model\Session;
use Magento\CatalogRule\Model\Rule\Condition\CombineFactory as CatalogCombineFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Rule\Model\AbstractModel;
use Magento\Rule\Model\Action\Collection;
use Mageplaza\QuickbooksOnline\Model\ResourceModel\Sync as ResourceSync;
use Mageplaza\QuickbooksOnline\Model\Rule\Condition\Customer\CombineFactory as CustomerCombineFactory;
use Mageplaza\QuickbooksOnline\Model\Rule\Condition\Invoice\CombineFactory as InvoiceCombineFactory;
use Mageplaza\QuickbooksOnline\Model\Rule\Condition\Order\CombineFactory as OrderCombineFactory;
use Mageplaza\QuickbooksOnline\Model\Rule\Condition\Tax\CombineFactory as TaxCombineFactory;
use Mageplaza\QuickbooksOnline\Model\Source\MagentoObject;

/**
 * Class Sync
 *
 * @package Mageplaza\QuickbooksOnline\Model
 */
class Sync extends AbstractModel
{
    /**
     * @var CatalogCombineFactory
     */
    protected $catalogCombineFactory;

    /**
     * @var CustomerCombineFactory
     */
    protected $customerCombineFactory;

    /**
     * @var OrderCombineFactory
     */
    protected $orderCombineFactory;

    /**
     * @var InvoiceCombineFactory
     */
    protected $invoiceCombineFactory;

    /**
     * @var TaxCombineFactory
     */
    protected $taxCombineFactory;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Sync constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param TimezoneInterface $localeDate
     * @param CatalogCombineFactory $catalogCombineFactory
     * @param CustomerCombineFactory $customerCombineFactory
     * @param OrderCombineFactory $orderCombineFactory
     * @param InvoiceCombineFactory $invoiceCombineFactory
     * @param TaxCombineFactory $taxCombineFactory
     * @param Session $session
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        CatalogCombineFactory $catalogCombineFactory,
        CustomerCombineFactory $customerCombineFactory,
        OrderCombineFactory $orderCombineFactory,
        InvoiceCombineFactory $invoiceCombineFactory,
        TaxCombineFactory $taxCombineFactory,
        Session $session,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->catalogCombineFactory  = $catalogCombineFactory;
        $this->customerCombineFactory = $customerCombineFactory;
        $this->orderCombineFactory    = $orderCombineFactory;
        $this->invoiceCombineFactory  = $invoiceCombineFactory;
        $this->taxCombineFactory      = $taxCombineFactory;
        $this->session                = $session;

        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(ResourceSync::class);
    }

    /**
     * @param string $quickbooksModule
     * @param string $magentoObject
     *
     * @return int
     */
    public function checkSync($quickbooksModule, $magentoObject)
    {
        $syncCollection = $this->getCollection()
            ->addFieldToFilter('quickbooks_module', $quickbooksModule)
            ->addFieldToFilter('magento_object', $magentoObject);

        return $syncCollection->getSize();
    }

    /**
     * @return mixed
     */
    public function getWebsiteIdPaymentRule()
    {
        $sync = $this->getCollection()
            ->addFieldToFilter('magento_object', MagentoObject::PAYMENT_METHOD)
            ->getFirstItem();

        return $sync->getWebsiteIds();
    }

    /**
     * @param string $formName
     *
     * @return string
     */
    public function getConditionsFieldSetId($formName = '')
    {
        return $formName . 'rule_conditions_fieldset_' . $this->getId();
    }

    /**
     * @return mixed
     */
    public function getConditionsInstance()
    {
        $conditionType = $this->getMagentoObject();

        if (!$conditionType) {
            $conditionType = $this->session->getMpQuickbooksMagentoObject();
        }

        switch ($conditionType) {
            case MagentoObject::ORDER:
                return $this->orderCombineFactory->create();
            case MagentoObject::INVOICE:
                return $this->invoiceCombineFactory->create();
            case MagentoObject::CUSTOMER:
                return $this->customerCombineFactory->create();
            case MagentoObject::PRODUCT:
                return $this->catalogCombineFactory->create();
            case MagentoObject::TAX:
                return $this->taxCombineFactory->create();
            default:
                return $this->invoiceCombineFactory->create();
        }
    }

    /**
     * @return Collection|null
     */
    public function getActionsInstance()
    {
        return null;
    }

    /**
     * Initialize rule model data from array
     *
     * @param array $data
     *
     * @return $this
     */
    public function loadPost(array $data)
    {
        $arr = $this->_convertFlatToRecursive($data);

        if (isset($arr['conditions'])) {
            $this->getConditions()->setConditions([])->loadArray($arr['conditions'][1]);
        }

        return $this;
    }

    /**
     * @return Collection|null
     */
    public function getActions()
    {
        return null;
    }

    /**
     * @return AbstractModel|void
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        parent::beforeSave();

        if ($this->hasWebsiteIds()) {
            $websiteIds = $this->getWebsiteIds();

            if (is_array($websiteIds) && !empty($websiteIds)) {
                $this->setWebsiteIds(implode(',', $websiteIds));
            }
        }
    }
}
