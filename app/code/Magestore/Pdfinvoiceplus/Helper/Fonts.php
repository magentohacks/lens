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

namespace Magestore\Pdfinvoiceplus\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * class Fonts
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Fonts extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * string
     */
//    const FONTS_DIR = 'mPdf/ttfonts/';
    const FONTS_DIR = 'mPdf/mpdf/mpdf/ttfonts/';

    /**
     *
     */
    const DEFAULT_FONT_CODE = 'dejavusanscondensed';

    /**
     *
     */
    const DEFAULT_FONT_FILE_NAME = 'DejaVuSansCondensed.ttf';

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * Fonts constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $filesystem
    )
    {
        parent::__construct($context);
        $this->_filesystem = $filesystem;
        $this->_construct();
    }

    /**
     *
     */
    protected function _construct()
    {
        $fontConfigPath = $this->_filesystem->getDirectoryWrite(DirectoryList::LIB_INTERNAL)
            ->getAbsolutePath('mPdf/config_fonts.php');

        require_once $fontConfigPath;
    }

    /**
     * @param $fontCode
     * @return string
     * @throws \Exception
     */
    public function getFont($fontCode)
    {
        $fontFileList = $this->fontdata[$fontCode];

        if (!is_null($fontFileList) && $this->_isFontFileExist($fontFileList['R'])) {
            return $fontCode;
        } else {
            if ($this->_isFontFileExist(self::DEFAULT_FONT_FILE_NAME)) {
                return self::DEFAULT_FONT_CODE;
            }

            throw new \Exception('Print custom PDF file failed: Your font is not available and also cannot load default font !');
        }
    }

    /**
     * @param $fontFileName
     * @return bool
     */
    protected function _isFontFileExist($fontFileName)
    {
        if (file_exists(
            $this->_filesystem->getDirectoryWrite(DirectoryList::LIB_INTERNAL)->getAbsolutePath(self::FONTS_DIR . $fontFileName)
        )) {
            return true;
        }
        return false;
    }
}