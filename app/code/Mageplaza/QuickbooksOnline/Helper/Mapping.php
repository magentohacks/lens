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
namespace Mageplaza\QuickbooksOnline\Helper;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as ProductAttributeCollection;
use Magento\Customer\Model\ResourceModel\Address\Attribute\Collection as CustomerAddressAttributeCollection;
use Magento\Customer\Model\ResourceModel\Attribute\Collection as CustomerAttributeCollection;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Escaper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\QuickbooksOnline\Model\ResourceModel\Sync as ResourceModelSync;
use Mageplaza\QuickbooksOnline\Model\Source\MagentoObject;
use Mageplaza\QuickbooksOnline\Model\Source\QuickbooksModule;
use Mageplaza\QuickbooksOnline\Model\Sync as SyncModel;

/**
 * Class Mapping
 * @package Mageplaza\QuickbooksOnline\Helper
 */
class Mapping extends AbstractData
{
    /**
     * Match options in {{ }}
     */
    const PATTERN_OPTIONS = '/{{([a-zA-Z_]{0,50})(.*?)}}/si';

    /***
     * @var TimezoneInterface
     */
    protected $date;

    /***
     * @var QuickbooksModule
     */
    protected $quickbooksOption;

    /**
     * @var ResourceModelSync
     */
    protected $resourceSync;

    /**
     * @var ProductAttributeCollection
     */
    protected $productAttributeCollection;

    /**
     * @var CustomerAttributeCollection
     */
    protected $customerAttributeCollection;

    /**
     * @var CustomerAddressAttributeCollection
     */
    protected $customerAddressAttributeCollection;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * Mapping constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param TimezoneInterface $date
     * @param StoreManagerInterface $storeManager
     * @param QuickbooksModule $quickbooksModule
     * @param ResourceModelSync $resourceSync
     * @param ProductAttributeCollection $productAttributeCollection
     * @param CustomerAttributeCollection $customerAttributeCollection
     * @param CustomerAddressAttributeCollection $customerAddressAttributeCollection
     * @param Escaper $escaper
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        TimezoneInterface $date,
        StoreManagerInterface $storeManager,
        QuickbooksModule $quickbooksModule,
        ResourceModelSync $resourceSync,
        ProductAttributeCollection $productAttributeCollection,
        CustomerAttributeCollection $customerAttributeCollection,
        CustomerAddressAttributeCollection $customerAddressAttributeCollection,
        Escaper $escaper
    ) {
        $this->date                               = $date;
        $this->quickbooksOption                   = $quickbooksModule;
        $this->resourceSync                       = $resourceSync;
        $this->productAttributeCollection         = $productAttributeCollection;
        $this->customerAttributeCollection        = $customerAttributeCollection;
        $this->customerAddressAttributeCollection = $customerAddressAttributeCollection;
        $this->escaper                            = $escaper;
        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param string $value
     *
     * @return array|mixed
     */
    public function matchData($value)
    {
        preg_match_all(self::PATTERN_OPTIONS, $value, $matches);

        if ($matches && isset($matches[1])) {
            return $matches[1];
        }

        return [];
    }

    /**
     * @return array
     */
    public function quickbooksObject()
    {
        return [
            'item'          => $this->getProductFieldsQuickbooks(),
            'customer'      => $this->getCustomerFieldsQuickbooks(),
            'creditMemo'    => $this->getCreditMemoFieldsQuickbooks(),
            'invoice'       => $this->getInvoiceFieldQuickbooks(),
            'salesReceipt'  => $this->getOrderFieldsQuickbooks(),
            'paymentMethod' => [],
            'taxService'    => $this->getTaxFieldsQuickbooks()
        ];
    }

    /**
     * @param string $object
     *
     * @return string
     */
    public function createMappingFields($object)
    {
        $mappings = $this->quickbooksObject()[$object];

        return $this->createFields($mappings);
    }

    /**
     * @param array $mappings
     *
     * @return string
     */
    public function createFields($mappings)
    {
        $html = '';

        foreach ($mappings as $key => $mappingData) {
            $html .= $this->createRow($key, $mappingData);
        }

        return $html;
    }

    /**
     * @param string $key
     * @param array $mappingData
     *
     * @return string
     */
    public function createRow($key, $mappingData)
    {
        $html = '<tr>';
        $html .= $this->createLabel($key, $mappingData);
        $html .= $this->createInput($key, 'value', $mappingData);
        $html .= $this->createInput($key, 'default', $mappingData);
        $html .= $this->createInput($key, 'description', $mappingData);
        $html .= '</tr>';

        return $html;
    }

    /**
     * @param string $key
     * @param array $value
     *
     * @return string
     */
    public function createLabel($key, $value)
    {
        return '<td>
                    <label class="admin__field-label mapping-label" for="' . $key . '">
                        <span>' . $value['label'] . '</span>
                    </label>
                </td>';
    }

    /**
     * @param string $key
     * @param string $name
     * @param array $data
     *
     * @return string
     */
    public function createInput($key, $name, $data)
    {
        $dataInit  = '';
        $nameInput = 'sync[mapping][' . $key . '][' . $name . ']';
        $comment   = '';

        if ($name === 'default' || $name === 'value') {
            $comment = '<div class="mp-field-comment">' . __('Accept %1 value.', $data['type']) . '</div>';
        }

        if ($name === 'default' && $data['type'] === 'date') {
            $dataInit = 'data-mage-init="' . $this->escaper->escapeHtml($this->initDate()) . '"';
        }

        $button = '';

        if ($name === 'value') {
            $button = $this->createButton($key, $data);
        }

        if ($name === 'default' && $data['type'] === 'boolean') {
            $options = ['true', 'false', ''];
            $input   = '<select id="' . $key . '-' . $name . '"
                        name="' . $nameInput . '"
                        title="' . $data['label'] . '"
                        class="select admin__control-select" ' . $dataInit . '
                        style="width:100%">';

            foreach ($options as $option) {
                $selected = $option === $data[$name] ? 'selected' : '';
                $input    .= '<option value="' . $option . '" ' . $selected . '>' . ucfirst($option) . '</option>';
            }

            $input .= '</select>';
        } else {
            $input = '<input id="' . $key . '-' . $name . '"
                        name="' . $nameInput . '"
                        title="' . $data['label'] . '"
                        type="text"
                        value="' . $data[$name] . '"
                        class="input-text admin__control-text" ' . $dataInit . '
                        style="width:100%">';
        }

        return '<td>
                     <div class="admin__field-control control" style="position: relative" >
                        ' . $input . $comment . $button . '
                     </div>
                </td>';
    }

    /**
     * @return string
     */
    public function initDate()
    {
        return self::jsonEncode(
            [
                'calendar' => [
                    'dateFormat'  => 'yyyy-MM-dd',
                    'showsTime'   => false,
                    'timeFormat'  => null,
                    'buttonImage' => null,
                    'buttonText'  => __('Select Date'),
                    'disabled'    => null,
                    'minDate'     => null,
                    'maxDate'     => null,
                ],
            ]
        );
    }

    /**
     * @param string $key
     * @param array $data
     *
     * @return string
     */
    public function createButton($key, $data)
    {
        $title     = __('Insert Variable...');
        $typeName  = 'sync[mapping][' . $key . '][type]';
        $typeValue = $data['type'];

        return '<button class="insert_variable"
                            title="' . $title . '"
                            target="' . $key . '"
                            type="button"
                            style="position: absolute;top: 0;right: -45px;">
                                <span>...</span>
                    </button>
                    <input type="hidden" name="' . $typeName . '" value="' . $typeValue . '" />
                ';
    }

    /**
     * @param SyncModel $sync
     *
     * @return string
     */
    public function getMappingFieldsByRule($sync)
    {
        if ($sync->getMagentoObject() === MagentoObject::PAYMENT_METHOD) {
            return '';
        }

        $mapping     = Data::jsonDecode($sync->getMapping());
        $mappingData = [];
        $quickbooks  = $this->quickbooksObject()[$sync->getQuickbooksModule()];

        foreach ($mapping as $key => $dataField) {
            $dataField['label'] = $quickbooks[$key]['label'];
            $dataField['type']  = $quickbooks[$key]['type'];
            $mappingData[$key]  = $dataField;
        }

        return $this->createFields($mappingData);
    }

    /**
     * @return array
     */
    public function getMappingObject()
    {
        $options = $this->quickbooksOption->toOptionArray();

        return [
            MagentoObject::CUSTOMER       => [$options[0]],
            MagentoObject::PRODUCT        => [$options[1]],
            MagentoObject::ORDER          => [$options[2]],
            MagentoObject::INVOICE        => [$options[3]],
            MagentoObject::CREDIT_MEMO    => [$options[4]],
            MagentoObject::PAYMENT_METHOD => [$options[5]],
            MagentoObject::TAX            => [$options[6]]
        ];
    }

    /**
     * @param string $type
     * @param bool $isJsonEncode
     *
     * @return array|string
     */
    public function getDefaultVariable($type, $isJsonEncode = false)
    {
        $data = [];

        switch ($type) {
            case QuickbooksModule::PRODUCT:
                $data = $this->getDefaultProductVariable();
                break;
            case QuickbooksModule::ORDER:
                $data = $this->getDefaultOrderVariable();
                break;
            case QuickbooksModule::INVOICE:
                $data = $this->getDefaultInvoiceVariable();
                break;
            case QuickbooksModule::CUSTOMER:
                $data = $this->getDefaultCustomerVariable();
                break;
            case QuickbooksModule::CREDIT_MEMO:
                $data = $this->getDefaultCreditMemoVariable();
                break;
            case QuickbooksModule::TAX:
                $data = $this->getDefaultTaxVariable();
                break;
        }

        return $isJsonEncode ? self::jsonEncode($data) : $data;
    }

    /**
     *  =========================================== QUICKBOOKS FIELDS =================================================
     */

    /**
     * @return array
     */
    public function getCustomerFieldsQuickbooks()
    {
        $quickbooksFields = [
            'DisplayName'             => [
                'label'       => __('Display Name'),
                'value'       => '{{firstname}}{{lastname}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string'
            ],
            'Title'                   => [
                'label'       => __('Title'),
                'value'       => '{{firstname}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string'
            ],
            'GivenName'               => [
                'label'       => __('Given Name'),
                'value'       => '{{firstname}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string'
            ],
            'MiddleName'              => [
                'label'       => __('Middle Name'),
                'value'       => '{{middlename}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'Suffix'                  => [
                'label'       => __('Suffix'),
                'value'       => '{{suffix}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string'
            ],
            'FamilyName'              => [
                'label'       => __('Family Name'),
                'value'       => '{{lastname}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string'
            ],
            'PrimaryEmailAddr'        => [
                'label'       => __('Email Address'),
                'value'       => '{{email}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string'
            ],
            'SecondaryTaxIdentifier'  => [
                'label'       => __('Secondary Tax Identifier'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'PreferredDeliveryMethod' => [
                'label'       => __('Preferred Delivery Method'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string'
            ],
            'GSTIN'                   => [
                'label'       => __('GSTIN'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string'
            ],
            'BusinessNumber'          => [
                'label'       => __('Business Number'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string'
            ],
            'BillWithParent'          => [
                'label'       => __('Bill With Parent'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string'
            ],
            'Mobile'                  => [
                'label'       => __('Mobile'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string'
            ],
            'Fax'                     => [
                'label'       => __('Fax'),
                'value'       => '{{shipping_fax}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'WebAddr'                 => [
                'label'       => __('Website Address'),
                'value'       => '{{website}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'Job'                     => [
                'label'       => __('Job'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'boolean'
            ],
            'BalanceWithJobs'         => [
                'label'       => __('Balance With Jobs'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'float',
            ],
            'PrimaryPhone'            => [
                'label'       => __('Phone'),
                'value'       => '{{shipping_telephone}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string'
            ],
            'Taxable'                 => [
                'label'       => __('Taxable'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'boolean',
            ],
            'Notes'                   => [
                'label'       => __('Notes'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'Active'                  => [
                'label'       => __('Active'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'boolean',
            ],
            'Balance'                 => [
                'label'       => __('Balance'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'float',
            ],
            'CompanyName'             => [
                'label'       => __('Company Name'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'FullyQualifiedName'      => [
                'label'       => __('Display Name'),
                'value'       => '{{firstname}}{{lastname}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'CreateTime'              => [
                'label'       => __('Create Time'),
                'value'       => '{{created_at}}',
                'default'     => '',
                'description' => '',
                'type'        => 'date',
            ],
            'LastUpdatedTime'         => [
                'label'       => __('Last Updated Time'),
                'value'       => '{{updated_at}}',
                'default'     => '',
                'description' => '',
                'type'        => 'date',
            ]
        ];

        return array_merge($quickbooksFields, $this->getAddressFields());
    }

    /**
     * @return array
     */
    public function getAddressFields()
    {
        return [
            'BillPostalCode'             => [
                'label'       => __('Billing Postal Code'),
                'value'       => '{{billing_postcode}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'BillCity'                   => [
                'label'       => __('Billing City'),
                'value'       => '{{billing_city}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'BillCountry'                => [
                'label'       => __('Billing Country'),
                'value'       => '{{billing_country_id}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'BillLine1'                  => [
                'label'       => __('Billing Street Line 1'),
                'value'       => '{{billing_street}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'BillLine2'                  => [
                'label'       => __('Billing Street Line 2'),
                'value'       => '{{billing_region}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'BillLine3'                  => [
                'label'       => __('Billing Street Line 3'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'BillLine4'                  => [
                'label'       => __('Billing Street Line 4'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'BillLine5'                  => [
                'label'       => __('Billing Street Line 5'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'BillLat'                    => [
                'label'       => __('Billing Latitude'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'BillLong'                   => [
                'label'       => __('Billing Longitude'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'BillCountrySubDivisionCode' => [
                'label'       => __('Billing Region'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'ShipPostalCode'             => [
                'label'       => __('Shipping Postal Code'),
                'value'       => '{{shipping_postcode}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'ShipCity'                   => [
                'label'       => __('Shipping City'),
                'value'       => '{{shipping_city}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'ShipCountry'                => [
                'label'       => __('Shipping Country'),
                'value'       => '{{shipping_country_id}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'ShipLine1'                  => [
                'label'       => __('Shipping Street Line 1'),
                'value'       => '{{shipping_street}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'ShipLine2'                  => [
                'label'       => __('Shipping Street Line 2'),
                'value'       => '{{shipping_region}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'ShipLine3'                  => [
                'label'       => __('Shipping Street Line 3'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'ShipLine4'                  => [
                'label'       => __('Shipping Street Line 4'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'ShipLine5'                  => [
                'label'       => __('Shipping Street Line 5'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'ShipLat'                    => [
                'label'       => __('Shipping Latitude'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'ShipLong'                   => [
                'label'       => __('Shipping Longitude'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'ShipCountrySubDivisionCode' => [
                'label'       => __('Shipping Region'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getProductFieldsQuickbooks()
    {
        $currentDate = $this->date->date()->format('Y-m-d');

        return [
            'Name'                => [
                'label'       => __('Name'),
                'value'       => '{{name}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'InvStartDate'        => [
                'label'       => __('Inventory Start Date'),
                'value'       => '',
                'default'     => $currentDate,
                'description' => '',
                'type'        => 'date',
            ],
            'Type'                => [
                'label'       => __('Type'),
                'value'       => '',
                'default'     => 'Inventory',
                'description' => __('Only accept 4 variables: Inventory, Service, NonInventory, Bundle'),
                'type'        => 'string',
            ],
            'QtyOnHand'           => [
                'label'       => __('Current Stock Qty'),
                'value'       => '{{qty}}',
                'default'     => '',
                'description' => '',
                'type'        => 'float',
            ],
            'Sku'                 => [
                'label'       => __('SKU'),
                'value'       => '{{sku}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'SalesTaxIncluded'    => [
                'label'       => __('Sales Tax (included in the item amount)'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'boolean',
            ],
            'TrackQtyOnHand'      => [
                'label'       => __('Quantity on hand to be tracked?'),
                'value'       => '',
                'default'     => 'true',
                'description' => '',
                'type'        => 'boolean',
            ],
            'PurchaseTaxIncluded' => [
                'label'       => __('Purchase Tax (included in the item amount)'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'boolean',
            ],
            'Description'         => [
                'label'       => __('Description'),
                'value'       => '{{description}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'SubItem'             => [
                'label'       => __('Sub Item'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'boolean',
            ],
            'Taxable'             => [
                'label'       => __('Taxable Transactions'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'boolean',
            ],
            'ReorderPoint'        => [
                'label'       => __('Reorder Point'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'float',
            ],
            'PurchaseDesc'        => [
                'label'       => __('Purchase Description'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'Active'              => [
                'label'       => __('Active'),
                'value'       => '{{status}}',
                'default'     => '',
                'description' => '',
                'type'        => 'boolean',
            ],
            'ServiceType'         => [
                'label'       => __('Service Type'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'PurchaseCost'        => [
                'label'       => __('Purchase Cost'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'float',
            ],
            'UnitPrice'           => [
                'label'       => __('Unit Price'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'float',
            ],
            'FullyQualifiedName'  => [
                'label'       => __('Fully Qualified Name'),
                'value'       => '{{name}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],

        ];
    }

    /**
     * @return array
     */
    public function getCreditMemoFieldsQuickbooks()
    {
        $fields = [
            'BillEmail'             => [
                'label'       => __('Billing Email Address'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'PrintStatus'           => [
                'label'       => __('Print Status'),
                'value'       => '',
                'default'     => 'NotSet',
                'description' => '',
                'type'        => 'string',
            ],
            'TotalAmt'              => [
                'label'       => __('Total Amount'),
                'value'       => '{{grand_total}}',
                'default'     => '',
                'description' => '',
                'type'        => 'float',
            ],
            'ApplyTaxAfterDiscount' => [
                'label'       => __('Tax (applied after discount)'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'boolean',
            ],
            'DocNumber'             => [
                'label'       => __('Transaction Reference Number'),
                'value'       => '{{increment_id}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'InvoiceRef'            => [
                'label'       => __('Invoice ID'),
                'value'       => '{{invoice_id}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'CurrencyRef'           => [
                'label'       => __('Currency'),
                'value'       => '{{order_currency_code}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'PrivateNote'           => [
                'label'       => __('Private Note'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'CustomerMemo'          => [
                'label'       => __('Customer Memo'),
                'value'       => '{{customer_note}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'TxnTaxDetail'          => [
                'label'       => __('Total Tax'),
                'value'       => '{{tax_amount}}',
                'default'     => '',
                'description' => '',
                'type'        => 'float',
            ],
            'ExchangeRate'          => [
                'label'       => __('Exchange Rate'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'float',
            ],
            'EmailStatus'           => [
                'label'       => __('Email Status'),
                'value'       => '',
                'default'     => 'NotSet',
                'description' => '',
                'type'        => 'string',
            ],
            'Balance'               => [
                'label'       => __('Balance'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'float',
            ],
            'CreateTime'            => [
                'label'       => __('Create Time'),
                'value'       => '{{created_at}}',
                'default'     => '',
                'description' => '',
                'type'        => 'date',
            ],
            'LastUpdatedTime'       => [
                'label'       => __('Last Updated Time'),
                'value'       => '{{updated_at}}',
                'default'     => '',
                'description' => '',
                'type'        => 'date',
            ],
        ];

        return array_merge($fields, $this->getAddressFields());
    }

    /**
     * @return array
     */
    public function getOrderFieldsQuickbooks()
    {
        $fields = [
            'DocNumber'             => [
                'label'       => __('Transaction Reference Number'),
                'value'       => '{{increment_id}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string'
            ],
            'CurrencyRef'           => [
                'label'       => __('Currency'),
                'value'       => '{{order_currency_code}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'CustomerRef'           => [
                'label'       => __('Customer ID'),
                'value'       => '{{customer_id}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'BillEmail'             => [
                'label'       => __('Billing Email'),
                'value'       => '{{customer_email}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'TrackingNum'           => [
                'label'       => __('Tracking Number'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'PrintStatus'           => [
                'label'       => __('Print Status'),
                'value'       => '',
                'default'     => 'NotSet',
                'description' => '',
                'type'        => 'string',
            ],
            'ApplyTaxAfterDiscount' => [
                'label'       => __('Tax (applied after discount)'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'boolean',
            ],
            'PrivateNote'           => [
                'label'       => __('Private Note'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'CustomerMemo'          => [
                'label'       => __('Customer Memo'),
                'value'       => '{{customer_note}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'EmailStatus'           => [
                'label'       => __('Email Status'),
                'value'       => '',
                'default'     => 'NotSet',
                'description' => '',
                'type'        => 'string',
            ],
            'TxnTaxDetail'          => [
                'label'       => __('Total Tax'),
                'value'       => '{{tax_amount}}',
                'default'     => '',
                'description' => '',
                'type'        => 'float',
            ],
            'ExchangeRate'          => [
                'label'       => __('Exchange Rate'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'float',
            ],
            'TotalAmt'              => [
                'label'       => __('Total Amount'),
                'value'       => '{{grand_total}}',
                'default'     => '',
                'description' => '',
                'type'        => 'float',
            ],
            'Balance'               => [
                'label'       => __('Balance'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'float',
            ],
            'CreateTime'            => [
                'label'       => __('Create Time'),
                'value'       => '{{created_at}}',
                'default'     => '',
                'description' => '',
                'type'        => 'date',
            ],
            'LastUpdatedTime'       => [
                'label'       => __('Last Updated Time'),
                'value'       => '{{updated_at}}',
                'default'     => '',
                'description' => '',
                'type'        => 'date',
            ]
        ];

        return array_merge($fields, $this->getAddressFields());
    }

    /**
     * @return array
     */
    public function getInvoiceFieldQuickbooks()
    {
        $fields = [
            'DocNumber'       => [
                'label'       => __('Transaction Reference Number'),
                'value'       => '{{increment_id}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string'
            ],
            'BillEmail'       => [
                'label'       => __('Billing Email'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string'
            ],
            'ShipDate'        => [
                'label'       => __('Ship Date'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'date',
            ],
            'TrackingNum'     => [
                'label'       => __('Tracking Number'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'PrintStatus'     => [
                'label'       => __('Print Status'),
                'value'       => '',
                'default'     => 'NotSet',
                'description' => '',
                'type'        => 'string',
            ],
            'CustomerMemo'    => [
                'label'       => __('Customer Memo'),
                'value'       => '{{customer_note}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'CurrencyRef'     => [
                'label'       => __('Currency'),
                'value'       => '{{order_currency_code}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'EmailStatus'     => [
                'label'       => __('Email Status'),
                'value'       => '',
                'default'     => 'NotSet',
                'description' => '',
                'type'        => 'string',
            ],
            'BillEmailCc'     => [
                'label'       => __('Billing Email Cc'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'Deposit'         => [
                'label'       => __('Deposit'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'float',
            ],
            'Balance'         => [
                'label'       => __('Balance'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'float',
            ],
            'TotalAmt'        => [
                'label'       => __('Total Amount'),
                'value'       => '{{grand_total}}',
                'default'     => '',
                'description' => '',
                'type'        => 'float',
            ],
            'ExchangeRate'    => [
                'label'       => __('Exchange Rate'),
                'value'       => '',
                'default'     => '',
                'description' => '',
                'type'        => 'float',
            ],
            'TxnTaxDetail'    => [
                'label'       => __('Total Tax'),
                'value'       => '{{tax_amount}}',
                'default'     => '',
                'description' => '',
                'type'        => 'float',
            ],
            'CreateTime'      => [
                'label'       => __('Create Time'),
                'value'       => '{{created_at}}',
                'default'     => '',
                'description' => '',
                'type'        => 'date',
            ],
            'LastUpdatedTime' => [
                'label'       => __('Last Updated Time'),
                'value'       => '{{updated_at}}',
                'default'     => '',
                'description' => '',
                'type'        => 'date',
            ]
        ];

        return array_merge($fields, $this->getAddressFields());
    }

    /**
     * @return array
     */
    public function getTaxFieldsQuickbooks()
    {
        return [
            'TaxCode'     => [
                'label'       => __('Tax Code'),
                'value'       => '{{code}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'RateValue'   => [
                'label'       => __('Rate Value'),
                'value'       => '{{rate}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ],
            'TaxRateName' => [
                'label'       => __('Tax Rate Name'),
                'value'       => '{{code}}',
                'default'     => '',
                'description' => '',
                'type'        => 'string',
            ]
        ];
    }

    /**
     *  =========================================== DEFAULT VALUES ====================================================
     */

    /**
     * @return array
     */
    public function getDefaultProductVariable()
    {
        $productAttributes = $this->productAttributeCollection->getItems();

        return [
            'label' => __('Product'),
            'value' => $this->getDataAttribute($productAttributes)
        ];
    }

    /**
     * @param object $attributes
     * @param null $type
     * @param null $prefix
     *
     * @return array
     */
    public function getDataAttribute($attributes, $type = null, $prefix = null)
    {
        $data  = [];
        $types = ['media_image', 'weee', 'swatch_visual', 'swatch_text', 'gallery', 'texteditor'];

        foreach ($attributes as $attribute) {
            if (in_array($attribute->getFrontendInput(), $types, true)) {
                continue;
            }

            $attributeCode            = $attribute->getAttributeCode();
            $customerIgnoreAttributes = [
                'disable_auto_group_change',
                'vat_is_valid',
                'vat_request_date',
                'vat_request_id',
                'vat_request_success'
            ];

            if ($type === 'customer' && in_array($attributeCode, $customerIgnoreAttributes, true)) {
                continue;
            }

            $prefixLabel = '';

            if ($prefix) {
                $attributeCode = $prefix . '_' . $attributeCode;
                $prefixLabel   = ucfirst($prefix) . ' ';
            }

            $label = $prefixLabel . $attribute->getFrontendLabel();

            if ($attribute->getAttributeCode() === 'region_id') {
                $label .= ' ID';
            }

            $data[] = [
                'value' => '{{' . $attributeCode . '}}',
                'label' => $label
            ];
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getDefaultCustomerVariable()
    {
        $customerAttributes         = $this->getDataAttribute(
            $this->customerAttributeCollection->getItems(),
            'customer'
        );
        $addressAttributes          = $this->customerAddressAttributeCollection->getItems();
        $customerBillingAttributes  = $this->getDataAttribute($addressAttributes, 'customer', 'billing');
        $customerShippingAttributes = $this->getDataAttribute($addressAttributes, 'customer', 'shipping');

        return [
            'label' => __('Customer'),
            'value' => array_merge($customerAttributes, $customerBillingAttributes, $customerShippingAttributes)
        ];
    }

    /**
     * @return array
     */
    public function getDefaultInvoiceVariable()
    {
        return [
            'label' => __('Invoice'),
            'value' => $this->resourceSync->getFieldTable('sales_invoice')
        ];
    }

    /**
     * @return array
     */
    public function getDefaultCreditMemoVariable()
    {
        return [
            'label' => __('Credit Memo'),
            'value' => $this->resourceSync->getFieldTable('sales_creditmemo')
        ];
    }

    /**
     * @return array
     */
    public function getDefaultTaxVariable()
    {
        return [
            'label' => 'Tax',
            'value' => $this->resourceSync->getFieldTable('tax_calculation_rate')
        ];
    }

    /**
     * @return array
     */
    public function getDefaultOrderVariable()
    {
        return [
            'label' => __('Order'),
            'value' => $this->resourceSync->getFieldTable('sales_order')
        ];
    }
}
