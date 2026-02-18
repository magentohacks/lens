<?php

namespace Konstant\Managefaq\Model;

/**
 * Category Model
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class Category extends \Magento\Framework\Model\AbstractModel
{
    const CATEGORY_TARGET_SELF = 0;
    const CATEGORY_TARGET_PARENT = 1;
    const CATEGORY_TARGET_BLANK = 2;
	
	protected $_categoryCollectionFactory;
	
    protected $_storeViewId = null;

    protected $_categoryFactory;

    protected $_formFieldHtmlIdPrefix = 'page_';
	
    protected $_storeManager;

    protected $_monolog;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Konstant\Managefaq\Model\ResourceModel\Category $resource,
        \Konstant\Managefaq\Model\ResourceModel\Category\Collection $resourceCollection,
		\Konstant\Managefaq\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Konstant\Managefaq\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Logger\Monolog $monolog
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
        $this->_categoryFactory = $categoryFactory;
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

    public function getStoreAttributes()
    {
        return array(
            'title',
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
            case self::CATEGORY_TARGET_SELF:
                return '_self';
            case self::CATEGORY_TARGET_PARENT:
                return '_parent';

            default:
                return '_blank';
        }
    }
}
