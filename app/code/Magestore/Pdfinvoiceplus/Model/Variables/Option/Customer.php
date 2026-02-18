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

namespace Magestore\Pdfinvoiceplus\Model\Variables\Option;

/**
 * class Customer
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Customer implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory
     */
    protected $_attributeCollectionFactory;

    /**
     * Customer constructor.
     *
     * @param \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory $attributeCollectionFactory
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory $attributeCollectionFactory
    )
    {
        $this->_attributeCollectionFactory = $attributeCollectionFactory;
    }

    /**
     * Get prefix variable
     *
     * @return string
     */
    public function getPrefixVariable()
    {
        return \Magestore\Pdfinvoiceplus\Model\Variables\AbstractVariableOption::PREFIX_VAR_CUSTOMER;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        /** @var \Magento\Customer\Model\ResourceModel\Attribute\Collection $collection */
        $collection = $this->_attributeCollectionFactory->create();

        $options = [];

        foreach ($collection as $item) {
            $options[] = [
                'value' => "{{var " . $this->getPrefixVariable() . "_{$item->getAttributeCode()}}}",
                'label' => $item->getFrontLabel()
                    ? $item->getFrontLabel() : __(ucwords(str_replace('_', ' ', $item->getAttributeCode())))
            ];
        }

        return $options;
    }
}