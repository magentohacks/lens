<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Pdfinvoiceplus\Model\ResourceModel;

use Magestore\Pdfinvoiceplus\Setup\InstallSchema;

/**
 * Class PdfTemplate
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class PdfTemplate extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(InstallSchema::SCHEMA_TEMPLATE, 'template_id');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\DB\Select
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->joinLeft(
            ['template_type' => $this->getTable(InstallSchema::SCHEMA_TEMPLATE_TYPE)],
            $this->getMainTable() . '.template_type_id = template_type.type_id',
            ['template_code' => 'template_type.code']
        );

        return $select;
    }

    /**
     * @param $templateTypeId
     *
     * @return string
     */
    public function loadTemplateCode(\Magento\Framework\Model\AbstractModel $object)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable(InstallSchema::SCHEMA_TEMPLATE_TYPE),
            'code'
        )->where('type_id = :type_id');

        $object->setData(
            'template_code',
            $this->getConnection()->fetchOne($select, [':type_id' => $object->getData('template_type_id')])
        );

        return $this;
    }
}
