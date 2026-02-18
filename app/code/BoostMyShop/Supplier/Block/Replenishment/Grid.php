<?php

namespace BoostMyShop\Supplier\Block\Replenishment;

use Magento\Backend\Block\Widget\Grid\Column;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_replenishmentCollection;
    protected $_supplierCollection;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory $userRolesFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \BoostMyShop\Supplier\Model\ResourceModel\Replenishment\CollectionFactory $replenishmentCollection,
        \BoostMyShop\Supplier\Model\ResourceModel\Supplier\CollectionFactory $supplierCollection,
        array $data = []
    ) {

        parent::__construct($context, $backendHelper, $data);

        $this->_replenishmentCollection = $replenishmentCollection;
        $this->_supplierCollection = $supplierCollection;
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('replenishmentGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
        $this->setTitle(__('Replenishment'));
        $this->setUseAjax(true);
    }


    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_replenishmentCollection->create()->init();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {

        $this->addColumn('image', ['header' => __('Image'),'filter' => false, 'sortable' => false, 'type' => 'renderer', 'renderer' => '\BoostMyShop\Supplier\Block\Replenishment\Renderer\Image']);
        $this->addColumn('sku', ['header' => __('Sku'), 'index' => 'sku', 'renderer' => 'BoostMyShop\Supplier\Block\Replenishment\Renderer\Sku']);
        $this->addColumn('name', ['header' => __('Product'), 'index' => 'name']);
        $this->addColumn('status', ['header' => __('Status'), 'index' => 'status', 'type' => 'options', 'options' => [0 => 'Disabled', 1 => 'Enabled']]);
        $this->addColumn('backorders', ['header' => __('Qty for backorders'), 'filter' => false, 'sortable' => false, 'index' => 'qty_for_backorder', 'align' => 'center']);
        $this->addColumn('low_stock', ['header' => __('Qty for low stock'), 'filter' => false, 'sortable' => false, 'index' => 'qty_for_low_stock', 'align' => 'center']);
        $this->addColumn('qty_to_receive', ['header' => __('Quantity to receive'), 'filter' => false, 'sortable' => false, 'index' => 'qty_to_receive', 'align' => 'center']);
        $this->addColumn('qty_for_po', ['header' => __('Quantity to order'), 'filter' => false, 'sortable' => false, 'index' => 'qty_to_order', 'align' => 'center']);
        $this->addColumn('stock_details', ['header' => __('Stock details'), 'filter' => false, 'sortable' => false, 'index' => 'entity_id', 'align' => 'left', 'renderer' => 'BoostMyShop\Supplier\Block\Replenishment\Renderer\StockDetails']);
        $this->addColumn('suppliers', ['header' => __('Suppliers'), 'index' => 'entity_id', 'sortable' => false, 'align' => 'left', 'renderer' => 'BoostMyShop\Supplier\Block\Replenishment\Renderer\Suppliers', 'filter' => 'BoostMyShop\Supplier\Block\Replenishment\Filter\Suppliers']);

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/Grid');
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setTemplate('Magento_Catalog::product/grid/massaction_extended.phtml');

        $suppliers = [];
        foreach($this->_supplierCollection->create()->setOrder('sup_name', 'ASC') as $supplier)
            $suppliers[$supplier->getId()] = $supplier->getsup_name();

        $this->getMassactionBlock()->addItem(
            'create_order',
            [
                'label' => __('Create new order'),
                'url' => $this->getUrl('*/*/createOrder', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'supplier',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Supplier'),
                        'values' => $suppliers
                    ]
                ]
            ]
        );


    }


}
