<?php

namespace BoostMyShop\Rma\Block\Rma\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Column;

class Items extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_coreRegistry = null;
    protected $_jsonEncoder;
    protected $_config;

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
        \Magento\Framework\Registry $coreRegistry,
        \BoostMyShop\Rma\Model\Config $config,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_coreRegistry = $coreRegistry;
        $this->_config = $config;
        parent::__construct($context, $backendHelper, $data);

        $this->setPagerVisibility(false);
        $this->setMessageBlockVisibility(false);
        $this->setDefaultLimit(200);
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rmaItemsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
        $this->setTitle(__('Items'));
        $this->setUseAjax(true);
    }

    protected function getRma()
    {
        return $this->_coreRegistry->registry('current_rma');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->getRma()->getAllItems();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {

        $this->addColumn('image', ['header' => __('Image'), 'sortable' => false, 'filter' => false, 'type' => 'renderer', 'renderer' => '\BoostMyShop\Rma\Block\Rma\Edit\Tab\Renderer\Image']);
        $this->addColumn('ri_sku', ['header' => __('Sku'), 'index' => 'ri_sku', 'sortable' => false, 'filter' => false]);
        $this->addColumn('ri_name', ['header' => __('Product'), 'index' => 'ri_name', 'sortable' => false, 'filter' => false]);
        $this->addColumn('ri_qty', ['header' => __('Quantity'), 'align' => 'center', 'index' => 'ri_qty', 'type' => 'renderer', 'renderer' => '\BoostMyShop\Rma\Block\Rma\Edit\Tab\Renderer\Quantity', 'sortable' => false, 'filter' => false]);
        $this->addColumn('ri_reason', ['header' => __('Reason'), 'index' => 'ri_reason', 'type' => 'options', 'options' => $this->_config->getReasons(), 'type' => 'renderer', 'renderer' => '\BoostMyShop\Rma\Block\Rma\Edit\Tab\Renderer\Reason', 'sortable' => false, 'filter' => false]);
        $this->addColumn('ri_request', ['header' => __('Request'), 'index' => 'ri_request', 'type' => 'options', 'options' => $this->_config->getRequests(), 'type' => 'renderer', 'renderer' => '\BoostMyShop\Rma\Block\Rma\Edit\Tab\Renderer\Request', 'sortable' => false, 'filter' => false]);
        $this->addColumn('ri_comments', ['header' => __('Comments'), 'index' => 'ri_comments', 'type' => 'renderer', 'renderer' => '\BoostMyShop\Rma\Block\Rma\Edit\Tab\Renderer\Comments', 'sortable' => false, 'filter' => false]);

        return parent::_prepareColumns();
    }

    public function getMainButtonsHtml()
    {
        //nothing, hide buttons
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/itemsGrid', ['rma_id' => $this->getRma()->getId()]);
    }

    public function getReasons()
    {
        ;
    }

}
