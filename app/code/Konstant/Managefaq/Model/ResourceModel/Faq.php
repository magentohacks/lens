<?php
namespace Konstant\Managefaq\Model\ResourceModel;

/**
 * FAQ Resource Model
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class Faq extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('konstant_managefaq_faq', 'id');
    }
}
