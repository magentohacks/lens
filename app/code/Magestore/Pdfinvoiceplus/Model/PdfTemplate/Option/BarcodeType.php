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
 * class BarcodeType
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class BarcodeType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     *
     */
    const BARCODE_QR = 'QR';
    /**
     *
     */
    const BARCODE_EAN13 = 'EAN13';
    /**
     *
     */
    const BARCODE_UPCA = 'UPCA';
    /**
     *
     */
    const BARCODE_EAN8 = 'EAN8';

    /**
     *
     */
    const BARCODE_IMB = 'IMB';
    /**
     *
     */
    const BARCODE_RM4SCC = 'RM4SCC';
    /**
     *
     */
    const BARCODE_KIX = 'KIX';

    /**
     *
     */
    const BARCODE_POSTNET = 'POSTNET';
    /**
     *
     */
    const BARCODE_PLANET = 'PLANET';

    /**
     *
     */
    const BARCODE_C128A = 'C128A';
    /**
     *
     */
    const BARCODE_EAN128A = 'EAN128A';
    /**
     *
     */
    const BARCODE_C39 = 'C39';
    /**
     *
     */
    const BARCODE_S25 = 'S25';
    /**
     *
     */
    const BARCODE_C93 = 'C93';
    /**
     *
     */
    const BARCODE_MSI = 'MSI';
    /**
     *
     */
    const BARCODE_CODABAR = 'CODABAR';
    /**
     *
     */
    const BARCODE_CODE11 = 'CODE11';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::BARCODE_QR, 'label' => __('QR')],
            ['value' => self::BARCODE_EAN13, 'label' => __('EAN-13')],
            ['value' => self::BARCODE_UPCA, 'label' => __('UPC-A')],
            ['value' => self::BARCODE_EAN8, 'label' => __('EAN-8')],
            ['value' => self::BARCODE_IMB, 'label' => __('Intelligent Mail Barcode')],
            ['value' => self::BARCODE_RM4SCC, 'label' => __('Royal Mail 4-state Customer Barcode')],
            ['value' => self::BARCODE_KIX, 'label' => __('Royal Mail 4-state Customer Barcode(Dutch)')],
            ['value' => self::BARCODE_POSTNET, 'label' => __('POSTNET')],
            ['value' => self::BARCODE_PLANET, 'label' => __('PLANET')],
            ['value' => self::BARCODE_C128A, 'label' => __('Code 128')],
            ['value' => self::BARCODE_EAN128A, 'label' => __('EAN-128')],
            ['value' => self::BARCODE_C39, 'label' => __('Code 39')],
            ['value' => self::BARCODE_S25, 'label' => __('Standard 2 of 5')],
            ['value' => self::BARCODE_C93, 'label' => __('Code 93')],
            ['value' => self::BARCODE_MSI, 'label' => __('MSI')],
            ['value' => self::BARCODE_CODABAR, 'label' => __('CODABAR')],
            ['value' => self::BARCODE_CODE11, 'label' => __('Code 11')],
        ];
    }
}