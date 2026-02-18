<?php

namespace Konstant\Managefaq\Model;

/**
 * FAQ Model
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class Faq extends \Magento\Framework\Model\AbstractModel
{
    const FAQ_TARGET_SELF = 0;
    const FAQ_TARGET_PARENT = 1;
    const FAQ_TARGET_BLANK = 2;
	
	protected $_categoryCollectionFactory;
	
    protected $_storeViewId = null;

    protected $_faqFactory;

    protected $_formFieldHtmlIdPrefix = 'page_';
	
    protected $_storeManager;

    protected $_monolog;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Konstant\Managefaq\Model\ResourceModel\Faq $resource,
        \Konstant\Managefaq\Model\ResourceModel\Faq\Collection $resourceCollection,
		\Konstant\Managefaq\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Konstant\Managefaq\Model\FaqFactory $faqFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Logger\Monolog $monolog
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
        $this->_faqFactory = $faqFactory;
        $this->_storeManager = $storeManager;
		$this->_categoryCollectionFactory = $categoryCollectionFactory;

        $this->_monolog = $monolog;

        if ($storeViewId = $this->_storeManager->getStore()->getId()) {
            $this->_storeViewId = $storeViewId;
        }
    }

    public function getFormFieldHtmlIdPrefix()
    {
        return $this->_formFieldHtmlIdPrefix;
    }
	
	public function getAvailableCategory()
    {
        $option[] = [
            'value' => '',
            'label' => __('-------- Please select a category --------'),
        ];

        $categoryCollection = $this->_categoryCollectionFactory->create();
        foreach ($categoryCollection as $category) {
            $option[] = [
                'value' => $category->getId(),
                'label' => $category->getTitle(),
            ];
        }
        return $option;
    }
	
    public function getStoreAttributes()
    {
        return array(
            'name',
            'status',
        );
    }

    public function getStoreViewId()
    {
        return $this->_storeViewId;
    }

    public function setStoreViewId($storeViewId)
    {
        $this->_storeViewId = $storeViewId;

        return $this;
    }

    public function load($id, $field = null)
    {
        parent::load($id, $field);
        if ($this->getStoreViewId()) {
            $this->getStoreViewValue();
        }

        return $this;
    }

    public function getTargetValue()
    {
        switch ($this->getTarget()) {
            case self::FAQ_TARGET_SELF:
                return '_self';
            case self::FAQ_TARGET_PARENT:
                return '_parent';

            default:
                return '_blank';
        }
    }
}
