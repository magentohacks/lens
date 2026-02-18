<?php
namespace Konstant\Managefaq\Block;

use Konstant\Managefaq\Model\Status;

/**
 * Managefaq Block
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class Managefaq extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'Konstant_Managefaq::managefaq.phtml';
	protected $_coreRegistry;
	protected $_categoryCollectionFactory;
	protected $_scopeConfig;
	protected $_storeManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
		\Konstant\Managefaq\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Konstant\Managefaq\Model\ResourceModel\Faq\CollectionFactory $faqCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
		$this->_categoryCollectionFactory = $categoryCollectionFactory;
		
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_storeManager = $context->getStoreManager();
    }
}
