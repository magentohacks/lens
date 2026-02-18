<?php

namespace Konstant\Managefaq\Model\ResourceModel\Faq;

/**
 * FAQ Collection
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_storeViewId = null;

    protected $_storeManager;

    protected $_addedTable = [];

    protected $_isLoadSliderTitle = FALSE;

    protected function _construct()
    {
        $this->_init('Konstant\Managefaq\Model\Faq', 'Konstant\Managefaq\Model\ResourceModel\Faq');
    }

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_storeManager = $storeManager;

        if ($storeViewId = $this->_storeManager->getStore()->getId()) {
            $this->_storeViewId = $storeViewId;
        }
    }

    protected function _beforeLoad()
    {
        return parent::_beforeLoad();
    }

    public function setOrderRandByFaqId()
    {
        $this->getSelect()->orderRand('main_table.id');

        return $this;
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

    public function addFieldToFilter($field, $condition = null)
    {
		$attributes = array(
            'name',
            'status',
			'maintable',
        );
        $storeViewId = $this->getStoreViewId();

        if (in_array($field, $attributes) && $storeViewId) {
            if (!in_array($field, $this->_addedTable)) {
                $sql = sprintf(
                    'main_table.id = %s.id AND %s.store_id = %s  AND %s.attribute_code = %s ',
                    $this->getConnection()->quoteTableAs($field),
                    $this->getConnection()->quoteTableAs($field),
                    $this->getConnection()->quote($storeViewId),
                    $this->getConnection()->quoteTableAs($field),
                    $this->getConnection()->quote($field)
                );
            }

            $fieldNullCondition = $this->_translateCondition("$field.value", ['null' => TRUE]);

            $mainfieldCondition = $this->_translateCondition("main_table.$field", $condition);

            $fieldCondition = $this->_translateCondition("$field.value", $condition);

            $condition = $this->_implodeCondition(
                $this->_implodeCondition($fieldNullCondition, $mainfieldCondition, \Zend_Db_Select::SQL_AND),
                $fieldCondition,
                \Zend_Db_Select::SQL_OR
            );

            $this->_select->where($condition, NULL, \Magento\Framework\DB\Select::TYPE_CONDITION);

            return $this;
        }
        if ($field == 'store_id') {
            $field = 'main_table.id';
        }

        return parent::addFieldToFilter($field, $condition);
    }

    protected function _implodeCondition($firstCondition, $secondCondition, $type)
    {
        return '(' . implode(') ' . $type . ' (', [$firstCondition, $secondCondition]) . ')';
    }

    public function getConnection()
    {
        return $this->getResource()->getConnection();
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        if ($storeViewId = $this->getStoreViewId()) {
            foreach ($this->_items as $item) {
                $item->setStoreViewId($storeViewId)->getStoreViewValue();
            }
        }

        return $this;
    }
}
