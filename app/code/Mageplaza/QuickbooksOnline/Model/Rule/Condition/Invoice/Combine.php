<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_QuickbooksOnline
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\QuickbooksOnline\Model\Rule\Condition\Invoice;

use Magento\Framework\Phrase;
use Magento\Rule\Model\Condition\Context;

/**
 * Class Combine
 * @package Mageplaza\QuickbooksOnline\Model\Rule\Condition\Invoice
 */
class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var Condition
     */
    protected $conditionInvoice;

    /**
     * Combine constructor.
     *
     * @param Context $context
     * @param Condition $conditionInvoice
     * @param array $data
     */
    public function __construct(
        Context $context,
        Condition $conditionInvoice,
        array $data = []
    ) {
        $this->conditionInvoice = $conditionInvoice;
        parent::__construct($context, $data);
        $this->setType($this->getCombineClass());
    }

    /**
     * Get new child select options
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $addressAttributes = $this->conditionInvoice->loadAttributeOptions()->getAttributeOption();
        $attributes        = [];

        foreach ($addressAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'Mageplaza\QuickbooksOnline\Model\Rule\Condition\Invoice\Condition|' . $code,
                'label' => $label,
            ];
        }

        $conditions = parent::getNewChildSelectOptions();

        return array_merge_recursive(
            $conditions,
            [
                [
                    'value' => $this->getCombineClass(),
                    'label' => __('Conditions combination')
                ],
                [
                    'label' => $this->getLabel(),
                    'value' => $attributes
                ]
            ]
        );
    }

    /**
     * @return string
     */
    public function getCombineClass()
    {
        return __CLASS__;
    }

    /**
     * @return Phrase
     */
    public function getLabel()
    {
        return __('Invoice');
    }
}
