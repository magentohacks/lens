<?php
namespace Konstant\Managefaq\Model\ResourceModel;

/**
 * Category Resource Model
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('konstant_managefaq_category', 'id');
    }
}
