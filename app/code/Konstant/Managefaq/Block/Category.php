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

class Category extends \Magento\Framework\View\Element\Template
{
	protected $_coreRegistry;
	
	protected $_categoryCollectionFactory;
	
	protected $_template = 'Konstant_Managefaq::faq_left.phtml';
	
	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Konstant\Managefaq\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
		parent::__construct($context, $data);
    }
	
	public function getCategory()
    {  
		return $this->_categoryCollectionFactory->create();
	}
}
