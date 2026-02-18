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

class Index extends \Magento\Framework\View\Element\Template
{
	protected $_coreRegistry;
	
	protected $_faqCollectionFactory;
	
	protected $_categoryCollectionFactory;
	
	protected $_template = 'Konstant_Managefaq::managefaq.phtml';
	
	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Konstant\Managefaq\Model\ResourceModel\Faq\CollectionFactory $faqCollectionFactory,
		\Konstant\Managefaq\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_faqCollectionFactory = $faqCollectionFactory;
		$this->_categoryCollectionFactory = $categoryCollectionFactory;
		parent::__construct($context, $data);
    }
    /**
     * Preparing global layout
     *
     * @return $this
     */
    public function _prepareLayout()
    {  
        $this->_addBreadcrumbs();
        $this->pageConfig->getTitle()->set('Faq');
        $this->pageConfig->setKeywords('');
        $this->pageConfig->setDescription('');
		
        return parent::_prepareLayout();
    }

    /**
     * Prepare breadcrumbs
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs()
    {
        if (($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs'))
        ) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'blog',
                [
                    'label' => __('FAQ'),
                    'title' => __(sprintf('Go to FAQ Home Page'))
                ]
            );
        }
    }
	
	public function getFaq()
    {
		if(isset($_GET) && !empty($_GET)) {
			return $this->_faqCollectionFactory->create()->addFieldToFilter('category_id', $_GET['id']);
		} else {
			$categoryCollection = $this->_categoryCollectionFactory->create();
			$categories = $categoryCollection->getData();
			foreach($categories as $category) {
				return $this->_faqCollectionFactory->create()->addFieldToFilter('category_id', $category['id']);
			}
		}
	}
}
