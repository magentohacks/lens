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
 * @package     Mageplaza_PdfInvoice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\PdfInvoice\Model\Source;

use horstoeko\zugferd\ZugferdProfiles;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class InvoiceProfile
 * @package Mageplaza\PdfInvoice\Model\Source
 */
class InvoiceProfile implements OptionSourceInterface
{
    const PROFILE_EN16931 = ZugferdProfiles::PROFILE_EN16931;
    const PROFILE_XRECHNUNG_3 = ZugferdProfiles::PROFILE_XRECHNUNG_3;

    /**
     * @return array
     */
    public function getOptionHash()
    {
        return [
            self::PROFILE_EN16931 => __('EN16931 (COMFORT)'),
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function toOptionArray()
    {
        $options = [];
        $descriptions = ZugferdProfiles::PROFILEDEF;

        foreach ($this->getOptionHash() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label,
                'title' => $descriptions[$value]['description'] ?? __('No description available')
            ];
        }

        return $options;
    }
}
