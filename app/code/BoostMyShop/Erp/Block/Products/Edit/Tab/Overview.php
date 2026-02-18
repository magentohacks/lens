<?php

namespace BoostMyShop\Erp\Block\Products\Edit\Tab;

class Overview extends \Magento\Backend\Block\Template
{
    protected $_template = 'Products/Edit/Tab/Overview.phtml';

    protected $_coreRegistry = null;
    protected $_productHelper = null;
    protected $_resultLayoutFactory = null;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry,
        \BoostMyShop\OrderPreparation\Model\Product $productHelper,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        array $data = [])
    {
        parent::__construct($context, $data);

        $this->_coreRegistry = $registry;
        $this->_productHelper = $productHelper;
        $this->_resultLayoutFactory = $resultLayoutFactory;
    }

    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    public function getImageUrl()
    {
        return $this->_productHelper->getImageUrl($this->getProduct()->getId());
    }

    public function getContent()
    {
        $layout = $this->_resultLayoutFactory->create();
        $layout->addHandle('erp_products_edit_overview');
        $layout->render();
        //$this->_view->loadLayout();
        //return $this->_view->renderLayout();
    }

}
