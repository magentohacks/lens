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
namespace Mageplaza\QuickbooksOnline\Model\Rule\Condition\Tax;

use Magento\Rule\Model\Condition\Context;

/**
 * Class Combine
 * @package Mageplaza\QuickbooksOnline\Model\Rule\Condition\Tax
 */
class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var Condition
     */
    protected $condition;

    /**
     * Combine constructor.
     *
     * @param Context $context
     * @param Condition $condition
     * @param array $data
     */
    public function __construct(
        Context $context,
        Condition $condition,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->condition = $condition;
        $this->setType(__CLASS__);
    }

    /**
     * Get new child select options
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $fields     = $this->condition->loadAttributeOptions()->getAttributeOption();
        $attributes = [];

        foreach ($fields as $code => $label) {
            $attributes[] = [
                'value' => 'Mageplaza\QuickbooksOnline\Model\Rule\Condition\Tax\Condition|' . $code,
                'label' => $label,
            ];
        }

        $conditions = parent::getNewChildSelectOptions();

        return array_merge_recursive(
            $conditions,
            [
                [
                    'value' => __CLASS__,
                    'label' => __('Conditions combination')
                ],
                [
                    'label' => __('Tax'),
                    'value' => $attributes
                ]
            ]
        );
    }
}
