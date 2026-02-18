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
namespace Mageplaza\QuickbooksOnline\Block\Adminhtml\Sync\Edit\Tab\Grid;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\QuickbooksOnline\Helper\Data as HelperData;
use Mageplaza\QuickbooksOnline\Model\ResourceModel\PaymentMethod\CollectionFactory as PaymentCollection;

/**
 * Class Payment
 * @package Mageplaza\QuickbooksOnline\Block\Adminhtml\Sync\Edit\Tab\Grid
 */
class Payment extends Extended
{
    /**
     * @var PaymentCollection
     */
    protected $paymentCollection;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * Payment constructor.
     *
     * @param Context $context
     * @param Data $backendHelper
     * @param PaymentCollection $paymentCollection
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        PaymentCollection $paymentCollection,
        HelperData $helperData,
        array $data = []
    ) {
        $this->paymentCollection = $paymentCollection;
        $this->helperData        = $helperData;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('paymentGrid');
        $this->setDefaultSort('method_id');
        $this->setUseAjax(true);
    }

    /**
     * @return Extended
     * @throws LocalizedException
     */
    protected function _prepareCollection()
    {
        $collection = $this->paymentCollection->create()
            ->addFieldToFilter('code', ['in' => array_keys($this->helperData->getPaymentMethods())]);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'method_id',
            [
                'header'   => __('ID'),
                'index'    => 'method_id',
                'sortable' => true,
                'type'     => 'number',
            ]
        );

        $this->addColumn(
            'code',
            [
                'header' => __('Code'),
                'index'  => 'code'
            ]
        );

        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'index'  => 'title'
            ]
        );

        $this->addColumn(
            'quickbooks_entity',
            [
                'header' => __('Quickbooks Entity'),
                'index'  => 'quickbooks_entity'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return $this|Extended
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('method_id');
        $this->getMassactionBlock()->setFormFieldName('method_id');

        $this->getMassactionBlock()->addItem(
            'add_to_queue',
            [
                'label'   => __('Add to Queue'),
                'url'     => $this->getUrl(
                    'mpquickbooks/sync/massAddPayment',
                    ['sync_id' => $this->getRequest()->getParam('id')]
                ),
                'confirm' => __('Are you sure you want to add to queue selected items?'),
            ]
        );

        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('mpquickbooks/sync/paymentGrid', ['_current' => true]);
    }

    /**
     * @param Product|DataObject $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }
}
