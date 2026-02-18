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
use Mageplaza\QuickbooksOnline\Block\Adminhtml\Sync\Edit\Tab\Grid\Render\QueueObject;
use Mageplaza\QuickbooksOnline\Block\Adminhtml\Sync\Edit\Tab\Grid\Render\Status;
use Mageplaza\QuickbooksOnline\Model\ResourceModel\Queue\CollectionFactory as QueueCollection;
use Mageplaza\QuickbooksOnline\Model\Source\MagentoObject;
use Mageplaza\QuickbooksOnline\Model\Source\QueueActions;
use Mageplaza\QuickbooksOnline\Model\Source\QueueStatus;
use Mageplaza\QuickbooksOnline\Model\Source\QuickbooksModule;

/**
 * Class Queue
 * @package Mageplaza\QuickbooksOnline\Block\Adminhtml\Sync\Edit\Tab\Grid
 */
class Queue extends Extended
{
    /**
     * @var QueueCollection
     */
    protected $queueCollection;

    /**
     * @var QueueStatus
     */
    protected $queueStatus;

    /**
     * @var QueueActions
     */
    protected $queueActions;

    /**
     * @var QuickbooksModule
     */
    protected $quickbooksModule;

    /**
     * Queue constructor.
     *
     * @param Context $context
     * @param Data $backendHelper
     * @param QueueCollection $queueCollection
     * @param QueueStatus $queueStatus
     * @param QueueActions $queueActions
     * @param QuickbooksModule $quickbooksModule
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        QueueCollection $queueCollection,
        QueueStatus $queueStatus,
        QueueActions $queueActions,
        QuickbooksModule $quickbooksModule,
        array $data = []
    ) {
        $this->queueCollection  = $queueCollection;
        $this->queueActions     = $queueActions;
        $this->queueStatus      = $queueStatus;
        $this->quickbooksModule = $quickbooksModule;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('queueGrid');
        $this->setDefaultSort('queue_id');
        $this->setUseAjax(true);
    }

    /**
     * @return Extended
     */
    protected function _prepareCollection()
    {
        $collection    = $this->queueCollection->create();
        $magentoObject = $this->getRequest()->getParam('magento_object');

        if ($magentoObject) {
            $id = $this->getRequest()->getParam('id');

            switch ($magentoObject) {
                case MagentoObject::ORDER:
                    $id = $this->getRequest()->getParam('order_id');
                    break;
                case MagentoObject::INVOICE:
                    $id = $this->getRequest()->getParam('invoice_id');
                    break;
                case MagentoObject::CREDIT_MEMO:
                    $id = $this->getRequest()->getParam('creditmemo_id');
                    break;
            }
            $collection->addFieldToFilter('object', $id)
                ->addFieldToFilter('magento_object', $magentoObject);
        } else {
            $collection->addFieldToFilter('sync_id', $this->getRequest()->getParam('id'));
        }

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
            'queue_id',
            [
                'header'   => __('ID'),
                'index'    => 'queue_id',
                'sortable' => true,
                'type'     => 'number',
            ]
        );

        $fullActionName = $this->getRequest()->getFullActionName();

        $this->addColumn(
            'object',
            [
                'header'   => __('Object'),
                'index'    => 'object',
                'filter'   => false,
                'renderer' => QueueObject::class,
                'options'  => $this->queueStatus->getOptionArray()
            ]
        );

        $this->addColumn(
            'status',
            [
                'header'   => __('Status'),
                'index'    => 'status',
                'renderer' => Status::class,
                'options'  => $this->queueStatus->getOptionArray(),
                'type'     => 'options',
            ]
        );

        if ($fullActionName === 'customer_index_edit') {
            $this->addColumn(
                'quickbooks_module',
                [
                    'header'  => __('Quickbooks Module'),
                    'index'   => 'quickbooks_module',
                    'options' => $this->quickbooksModule->getOptionArray(),
                    'type'    => 'options',
                ]
            );
        }

        $this->addColumn(
            'action',
            [
                'header'  => __('Events'),
                'index'   => 'action',
                'type'    => 'options',
                'options' => $this->queueActions->getOptionArray()
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header' => __('Created On'),
                'index'  => 'created_at',
                'type'   => 'datetime',
            ]
        );

        $this->addColumn(
            'updated_at',
            [
                'header' => __('Updated On'),
                'index'  => 'updated_at',
                'type'   => 'datetime',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('mpquickbooks/sync/queueGrid', ['_current' => true]);
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
