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

namespace Magestore\Pdfinvoiceplus\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magestore\Pdfinvoiceplus\Model\PdfTemplate\Option\PageOrientation;

/**
 * class MPdfPrinter
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class MPdfPrinter extends \Magento\Framework\DataObject implements MPdfPrinterInterface
{
    /**
     *
     */
    const ORIENTATION_PORTRAIT_CODE = 'P';

    /**
     *
     */
    const ORIENTATION_LANDSCAPE_CODE = 'L';

    /**
     * @var string
     */
    protected $_filename = 'mpdf.pdf';

    /**
     * @var bool
     */
    protected $_enablePageNumbering = false;

    /**
     * @var \mPDF
     */
    protected $_mpdfObject;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var string
     */
    protected $_mode = '';

    /**
     * @var string
     */
    protected $_format = 'A4';

    /**
     * @var int
     */
    protected $_top = 5;

    /**
     * @var int
     */
    protected $_bottom = 0;

    /**
     * @var int
     */
    protected $_left = 0;

    /**
     * @var int
     */
    protected $_right = 0;

    /**
     * @var string
     */
    protected $_orientation = 'P';

    /**
     * @var int
     */
    protected $_defaultFontSize = 8;

    /**
     * @var string
     */
    protected $_defaultFont = '';

    /**
     * @var int
     */
    protected $_marginHeader = 9;

    /**
     * @var int
     */
    protected $_marginFooter = 9;

    /**
     * @var \Magestore\Pdfinvoiceplus\Model\SystemConfig
     */
    protected $_systemConfig;

    /**
     * @var \Magestore\Pdfinvoiceplus\Helper\Fonts
     */
    protected $_fontsHelper;

    /**
     * MPdfPrinter constructor.
     *
     * @param \Magento\Framework\Filesystem $filesystem
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magestore\Pdfinvoiceplus\Model\SystemConfig $systemConfig,
        \Magestore\Pdfinvoiceplus\Helper\Fonts $fontsHelper,
        array $data = []
    )
    {
        parent::__construct($data);
        $this->_filesystem = $filesystem;
        $this->_systemConfig = $systemConfig;
        $this->_fontsHelper = $fontsHelper;
        $this->_construct();
    }

    /**
     *
     */
    protected function _construct()
    {
        if (!class_exists('mPDF', false)) {
//            $classPath = $this->_filesystem->getDirectoryWrite(DirectoryList::LIB_INTERNAL)
//                ->getAbsolutePath('mPdf/mpdf.php');

            $classPath = $this->_filesystem->getDirectoryWrite(DirectoryList::LIB_INTERNAL)
                ->getAbsolutePath('mPdf/autoload.php');

            require_once $classPath;
        }

        if ($this->hasData('filename')) {
            $this->setFilename($this->getData('filename'));
        }

        if ($this->hasData('mode')) {
            $this->setMode($this->getData('mode'));
        }

        if ($this->hasData('format')) {
            $this->setFormat($this->getData('format'));
        }

        if ($this->hasData('top')) {
            $this->setTop($this->getData('top'));
        }

        if ($this->hasData('bottom')) {
            $this->setBottom($this->getData('bottom'));
        }

        if ($this->hasData('left')) {
            $this->setLeft($this->getData('left'));
        }

        if ($this->hasData('right')) {
            $this->setRight($this->getData('right'));
        }

        if ($this->hasData('orientation')) {
            $this->setOrientation($this->getData('orientation'));
        }

        if ($this->hasData('default_font_size')) {
            $this->setDefaultFontSize($this->getData('default_font_size'));
        }

        $this->setDefaultFont($this->getPdfFont());

        if ($this->hasData('margin_header')) {
            $this->setMarginHeader($this->getData('margin_header'));
        }

        if ($this->hasData('margin_footer')) {
            $this->setMarginFooter($this->getData('margin_footer'));
        }

        if ($this->hasData('enable_page_numbering')) {
            $this->setEnablePageNumbering($this->getData('enable_page_numbering'));
        }

        $this->initMpdfObject();
    }

    /**
     * @return \mPDF
     */
    public function initMpdfObject()
    {
//        $this->_mpdfObject = new \mPDF(
//            $this->getMode(),
//            $this->getFormat(),
//            $this->getDefaultFontSize(),
//            $this->getDefaultFont(),
//            $this->getLeft(),
//            $this->getRight(),
//            $this->getBottom(),
//            $this->getMarginFooter(),
//            $this->getMarginHeader()
//        );

        $this->_mpdfObject = new \Mpdf\Mpdf(
            array(
                'mode' => $this->getMode(),
                'format' => $this->getFormat(),
                'default_font_size' => $this->getDefaultFontSize(),
                'default_font' => $this->getDefaultFont(),
                'mgl' => $this->getLeft(),
                'mgr' => $this->getRight(),
                'mgb' => $this->getBottom(),
                'mgf' => $this->getMarginFooter(),
                'mgh' => $this->getMarginHeader()
            )
        );
        $this->_mpdfObject->curlAllowUnsafeSslRequests = true;

        if ($this->isEnablePageNumbering()) {
            $this->_mpdfObject
                ->SetHTMLFooter('<div style = "float:right;z-index:16000 !important; width:30px; margin-right: 10px;">{PAGENO}/{nb}</div>');
        }

        return $this->_mpdfObject;
    }

    /**
     * @param string $html
     *
     * @return string
     */
    public function printPdf($html = '')
    {
        $this->writeHtml($html);

        return $this->outputPdf();
    }

    /**
     * @param      $html
     * @param int $sub
     * @param bool $init
     * @param bool $close
     */
    public function writeHtml($html, $sub = 0, $init = true, $close = true)
    {
        $this->_mpdfObject->WriteHTML($html, $sub, $init, $close);
    }

    /**
     * @param string $name
     * @param string $dest
     *
     * @return string
     */
    public function outputPdf($name = '', $dest = 'S')
    {
        if ($name) {
            $this->setFilename($name);
        }

        return $this->_mpdfObject->Output($this->getFilename(), $dest);
    }

    /**
     * @return \Magento\Framework\Filesystem
     */
    public function getFilesystem()
    {
        return $this->_filesystem;
    }

    /**
     * @return int
     */
    public function getBottom()
    {
        return $this->_bottom;
    }

    /**
     * @return int
     */
    public function getLeft()
    {
        return $this->_left;
    }

    /**
     * @return string
     */
    public function getOrientation()
    {
        return $this->_orientation;
    }

    /**
     * @return int
     */
    public function getDefaultFontSize()
    {
        return $this->_defaultFontSize;
    }

    /**
     * @return int
     */
    public function getMarginHeader()
    {
        return $this->_marginHeader;
    }

    /**
     * @return int
     */
    public function getMarginFooter()
    {
        return $this->_marginFooter;
    }

    /**
     * @param string $mode
     *
     * @return MPdfPrinter
     */
    public function setMode($mode)
    {
        $this->_mode = $mode;

        return $this;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->_mode;
    }

    /**
     * @param string $format
     *
     * @return MPdfPrinter
     */
    public function setFormat($format)
    {
        $this->_format = $format;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->_format;
    }

    /**
     * @param int $top
     *
     * @return MPdfPrinter
     */
    public function setTop($top)
    {
        $this->_top = $top;

        return $this;
    }

    /**
     * @return int
     */
    public function getTop()
    {
        return $this->_top;
    }

    /**
     * @param int $bottom
     *
     * @return MPdfPrinter
     */
    public function setBottom($bottom)
    {
        $this->_bottom = $bottom;

        return $this;
    }

    /**
     * @param int $left
     *
     * @return MPdfPrinter
     */
    public function setLeft($left)
    {
        $this->_left = $left;

        return $this;
    }

    /**
     * @param int $right
     *
     * @return MPdfPrinter
     */
    public function setRight($right)
    {
        $this->_right = $right;

        return $this;
    }

    /**
     * @return int
     */
    public function getRight()
    {
        return $this->_right;
    }

    /**
     * @param string $orientation
     *
     * @return MPdfPrinter
     */
    public function setOrientation($orientation)
    {
        if ($orientation == PageOrientation::PAGE_LANDSCAPE) {
            $this->_orientation = self::ORIENTATION_LANDSCAPE_CODE;

            return $this;
        }
        $this->_orientation = self::ORIENTATION_PORTRAIT_CODE;

        return $this;
    }

    /**
     * @param int $defaultFontSize
     *
     * @return MPdfPrinter
     */
    public function setDefaultFontSize($defaultFontSize)
    {
        $this->_defaultFontSize = $defaultFontSize;

        return $this;
    }

    /**
     * @param int $marginHeader
     *
     * @return MPdfPrinter
     */
    public function setMarginHeader($marginHeader)
    {
        $this->_marginHeader = $marginHeader;

        return $this;
    }

    /**
     * @param int $marginFooter
     *
     * @return MPdfPrinter
     */
    public function setMarginFooter($marginFooter)
    {
        $this->_marginFooter = $marginFooter;

        return $this;
    }

    /**
     * @param string $defaultFont
     *
     * @return MPdfPrinter
     */
    public function setDefaultFont($defaultFont)
    {
        $this->_defaultFont = $defaultFont;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultFont()
    {
        return $this->_defaultFont;
    }

    /**
     * @param string $filename
     *
     * @return MPdfPrinter
     */
    public function setFilename($filename)
    {
        $this->_filename = $filename;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->_filename;
    }

    /**
     * @param boolean $enablePageNumbering
     *
     * @return MPdfPrinter
     */
    public function setEnablePageNumbering($enablePageNumbering)
    {
        $this->_enablePageNumbering = $enablePageNumbering;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEnablePageNumbering()
    {
        return $this->_enablePageNumbering;
    }

    /**
     * @return \mPDF
     */
    public function getMpdfObject()
    {
        return $this->_mpdfObject;
    }

    /**
     * @param string $orientation
     * @param string $newformat
     */
    public function addPage(
        $orientation = '',
        $newformat = ''
    )
    {
        $this->setFormat($newformat);
        $this->setOrientation($orientation);

        $this->_mpdfObject->AddPage(
            $this->getOrientation(),
            $condition = '',
            $resetpagenum = '',
            $pagenumstyle = '',
            $suppress = '',
            $mgl = '',
            $mgr = '',
            $mgt = '',
            $mgb = '',
            $mgh = '',
            $mgf = '',
            $ohname = '',
            $ehname = '',
            $ofname = '',
            $efname = '',
            $ohvalue = 0,
            $ehvalue = 0,
            $ofvalue = 0,
            $efvalue = 0,
            $pagesel = '',
            $this->getFormat()
        );
    }

    /**
     * @return string
     */
    public function getPdfFont()
    {
        $fontConfig = $this->_systemConfig->getPdfFontFamily();
        return $this->_fontsHelper->getFont($fontConfig);
    }
}