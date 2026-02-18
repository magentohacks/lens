<?php

namespace BoostMyShop\Rma\Block\Rma;

use Magento\Backend\Block\Widget\Grid\Column;

class SelectOrder extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_orderCollection;
    protected $_storeCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection,
        array $data = []
    ) {

        parent::__construct($context, $backendHelper, $data);

        $this->_orderCollection = $orderCollection;
        $this->_storeCollectionFactory = $storeCollectionFactory;
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('orderGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
        $this->setTitle(__('Orders'));
    }


    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_orderCollection->create();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {

        $this->addColumn('entity_id', ['header' => __('ID'), 'index' => 'increment_id']);
        $this->addColumn('store_id', ['header' => __('Purchase Point'), 'index' => 'store_id', 'type' => 'options', 'options' => $this->getStoreOptions()]);
        $this->addColumn('created_at', ['header' => __('Purchase Date'), 'index' => 'created_at']);
        $this->addColumn('customer_name', ['header' => __('Customer'), 'index' => 'customer_name', 'renderer' => '\BoostMyShop\Rma\Block\Rma\Renderer\SelectOrder\CustomerName']);
        $this->addColumn('grand_total', ['header' => __('Grand Total'), 'index' => 'grand_total']);
        $this->addColumn('products', ['header' => __('Products'), 'index' => 'products', 'renderer' => '\BoostMyShop\Rma\Block\Rma\Renderer\SelectOrder\Products']);
        $this->addColumn('status', ['header' => __('Status'), 'index' => 'status']);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/createFromOrder', ['order_id' => $row->getId()]);
    }

    public function getStoreOptions()
    {
        $options = [];
        $options[''] = ' ';
        foreach($this->_storeCollectionFactory->create() as $item)
        {
            $options[$item->getId()] = $item->getname();
        }
        return $options;
    }

}
