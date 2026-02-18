<?php

namespace BoostMyShop\Supplier\Block\Order\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Column;

class AddProducts extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_coreRegistry = null;

    protected $_productFactory = null;

    protected $_orderProductCollectionFactory = null;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

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
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory,
        \BoostMyShop\Supplier\Model\ResourceModel\Order\Product\CollectionFactory $orderProductCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_productFactory = $productFactory;
        $this->_orderProductCollectionFactory = $orderProductCollectionFactory;
        $this->_coreRegistry = $coreRegistry;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
        $this->setTitle(__('Add products to purchase order'));
        $this->setUseAjax(true);

    }

    protected function getOrder()
    {
        return $this->_coreRegistry->registry('current_purchase_order');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_productFactory->create();
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('image');
        $collection->addAttributeToSelect('thumbnail');

        $collection->addFieldToFilter('type_id', array('in' => array('simple')));

        $alreadyAddedProducts = $this->_orderProductCollectionFactory->create()->getAlreadyAddedProductIds($this->getOrder()->getId());

        if (count($alreadyAddedProducts) > 0)
        $collection->addFieldToFilter('entity_id', array('nin' => $alreadyAddedProducts));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {

        $this->addColumn(
            'in_products',
            [
                'header' => __('Select'),
                'renderer' => 'BoostMyShop\Supplier\Block\Order\Edit\Tab\AddProducts\Renderer\Checkbox',
                'index' => 'entity_id',
                'sortable' => false,
                'filter' => false,
                'align' => 'center',
            ]
        );

        $this->addColumn(
            'qty',
            [
                'filter' => false,
                'sortable' => false,
                'header' => __('Quantity'),
                'renderer' => 'BoostMyShop\Supplier\Block\Order\Edit\Tab\AddProducts\Renderer\Qty',
                'name' => 'qty',
                'inline_css' => 'qty',
                'filter' => false,
                'align' => 'center',
                'type' => 'input',
                'validate_class' => 'validate-number',
                'index' => 'qty'
            ]
        );

        $this->addColumn('image', ['header' => __('Image'),'filter' => false, 'sortable' => false, 'type' => 'renderer', 'renderer' => '\BoostMyShop\Supplier\Block\Order\Edit\Tab\AddProducts\Renderer\Image']);
        $this->addColumn('sku', ['header' => __('Sku'), 'index' => 'sku', 'type' => 'text', 'renderer' => '\BoostMyShop\Supplier\Block\Order\Edit\Tab\AddProducts\Renderer\Sku']);
        $this->addColumn('name', ['header' => __('Name'), 'index' => 'name', 'type' => 'text']);
        $this->addColumn('stock_details', ['header' => __('Stock details'),'filter' => false, 'sortable' => false, 'type' => 'renderer', 'renderer' => '\BoostMyShop\Supplier\Block\Order\Edit\Tab\AddProducts\Renderer\StockDetails']);
        $this->addColumn('suppliers', ['header' => __('Suppliers'), 'index' => 'entity_id', 'sortable' => false, 'filter' => '\BoostMyShop\Supplier\Block\Order\Edit\Tab\AddProducts\Filter\Suppliers', 'type' => 'renderer', 'renderer' => '\BoostMyShop\Supplier\Block\Order\Edit\Tab\AddProducts\Renderer\Suppliers']);

        return parent::_prepareColumns();
    }


    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/addProductsGrid', ['po_id' => $this->getOrder()->getId()]);
    }

}
