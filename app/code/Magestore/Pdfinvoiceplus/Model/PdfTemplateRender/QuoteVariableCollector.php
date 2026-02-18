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

namespace Magestore\Pdfinvoiceplus\Model\PdfTemplateRender;

/**
 * class QuoteVariableCollector
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class QuoteVariableCollector extends \Magento\Framework\DataObject
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_stdTimeZone;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $stdTimeZone,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    )
    {
        parent::__construct($data);
        $this->_stdTimeZone = $stdTimeZone;
        $this->_objectManager = $objectManager;
    }

    /**
     * @return array
     */
    public function getQuoteVariables()
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->getData('quote');

        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->_objectManager->get('Magento\Sales\Model\Order');

        $variables = [];

        if ($quote) {
            foreach ($quote->getData() as $key => $value) {
                switch ($key) {
                    case 'created_at':
                        $variables['quote_' . $key] = ['value' => $this->_formatDate($quote->getCreatedAt())];
                        break;
                    case 'grand_total':
                        $variables['quote_' . $key] = ['value' => $order->formatPriceTxt($quote->getGrandTotal())];
                        break;

                    default:
                        $variables['quote_' . $key] = ['value' => $value];
                        break;
                }
            }
        }

        return $this->arrayToStandard($variables);
    }

    /**
     * @param null $date
     * @param int $format
     * @param bool $showTime
     */
    protected function _formatDate($date = null, $locale = null, $useTimezone = true)
    {

        $date = $this->_stdTimeZone->date($date, $locale, $useTimezone);

        return $date->format('Y-m-d_H-i-s');
    }

    /**
     * @param array $variable
     *
     * @return array
     */
    protected function arrayToStandard($variable = [])
    {
        $variables = [];
        foreach ($variable as $key => $var) {
            $variables[] = [$key => $var];
        }

        return $variables;
    }
}