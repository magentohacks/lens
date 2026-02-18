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

use Magento\Directory\Model\Config\Source\Allregion;
use Magento\Directory\Model\Config\Source\Country;
use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;

/**
 * Class Condition
 * @package Mageplaza\QuickbooksOnline\Model\Rule\Condition\Tax
 */
class Condition extends AbstractCondition
{
    /**
     * @var Country
     */
    protected $country;

    /**
     * @var Allregion
     */
    protected $region;

    /**
     * Condition constructor.
     *
     * @param Context $context
     * @param Country $country
     * @param Allregion $region
     * @param array $data
     */
    public function __construct(
        Context $context,
        Country $country,
        Allregion $region,
        array $data = []
    ) {
        $this->country = $country;
        $this->region  = $region;
        parent::__construct($context, $data);
    }

    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            'tax_postcode'   => __('Zip/Post Code'),
            'tax_country_id' => __('Country'),
            'tax_region_id'  => __('State'),
            'rate'           => __('Rate')
        ];
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return AbstractCondition
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    /**
     * Get input type
     *
     * @return string
     */
    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'tax_postcode':
            case 'tax_country_id':
            case 'tax_region_id':
            case 'rate':
                return 'numeric';
        }

        return 'string';
    }

    /**
     * Get attribute value input element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'tax_country_id':
            case 'tax_region_id':
                return 'select';
        }

        return 'text';
    }

    /**
     * @return array|mixed
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'tax_country_id':
                    $options = $this->country->toOptionArray();
                    break;
                case 'tax_region_id':
                    $options = $this->region->toOptionArray();
                    break;
                default:
                    $options = [];
            }

            $this->setData('value_select_options', $options);
        }

        return $this->getData('value_select_options');
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        if ($this->getInputType() === 'date' && !$this->getIsValueParsed()) {
            $this->setValue($this->formatDate($this->getData('value')));
            $this->setIsValueParsed(true);
        }

        return $this->getData('value');
    }

    /**
     * Check if attribute value should be explicit
     *
     * @return bool
     */
    public function getExplicitApply()
    {
        if (in_array($this->getAttribute(), ['created_at', 'order_date'], true)) {
            return true;
        }

        return false;
    }

    /**
     * @param AbstractModel $model
     *
     * @return bool
     */
    public function validate(AbstractModel $model)
    {
        switch ($this->getAttribute()) {
            case 'tax_region_id':
                if ($model->getShippingAddress()) {
                    $model->setShippingRegionId($model->getShippingAddress()->getRegionId());
                }
                break;
            case 'tax_country_id':
                if ($model->getShippingAddress()) {
                    $model->setShippingCountryId($model->getShippingAddress()->getCountryId());
                }
                break;
        }

        return parent::validate($model);
    }
}
