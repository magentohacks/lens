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

namespace Magestore\Pdfinvoiceplus\Model\PdfTemplate\Option;

/**
 * class Language
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Language implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Locale
     */
    protected $_locale;

    /**
     * @var \Magestore\Pdfinvoiceplus\Model\Localization
     */
    protected $_localization;

    /**
     * Language constructor.
     * @param \Magento\Config\Model\Config\Source\Locale $locale
     * @param \Magestore\Pdfinvoiceplus\Model\Localization $localization
     */
    public function __construct(
        \Magento\Config\Model\Config\Source\Locale $locale,
        \Magestore\Pdfinvoiceplus\Model\Localization $localization
    )
    {
        $this->_locale = $locale;
        $this->_localization = $localization;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = [];
        $listLocale = $this->_localization->getListLocale();
        foreach ($this->_locale->toOptionArray() as $option) {
            if (in_array($option['value'], $listLocale)) {
                $options[] = $option;
            }
        }

        return $options;
    }
}