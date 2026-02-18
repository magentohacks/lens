<?php

namespace BoostMyShop\Rma\Block\Rma\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Column;

class History extends \Magento\Backend\Block\Widget\Grid\Extended
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
        $this->setId('rmaHistoryrid');
        $this->setDefaultSort('rh_id', 'DESC');
        $this->setDefaultDir('desc');
        $this->setTitle(__('History'));
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
        $collection = $this->getRma()->getHistory();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {

        $this->addColumn('rh_id', ['header' => __('#'), 'index' => 'rh_id', 'sortable' => false, 'filter' => false]);
        $this->addColumn('rh_date', ['header' => __('Date'), 'index' => 'rh_date', 'sortable' => false, 'filter' => false]);
        $this->addColumn('rh_details', ['header' => __('Details'), 'index' => 'rh_details', 'sortable' => false, 'filter' => false]);

        return parent::_prepareColumns();
    }

    public function getMainButtonsHtml()
    {
        //nothing, hide buttons
    }



}
