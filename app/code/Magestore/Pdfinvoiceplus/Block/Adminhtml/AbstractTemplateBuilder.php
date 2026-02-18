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

namespace Magestore\Pdfinvoiceplus\Block\Adminhtml;

use Magento\Store\Model\ScopeInterface;
use Magestore\Pdfinvoiceplus\Model\PdfTemplate\Option\BarcodeType;
use Magestore\Pdfinvoiceplus\Model\PdfTemplate\Option\ShowBarcode;
use Magestore\Pdfinvoiceplus\Model\PdfTemplate\Option\PageOrientation;

/**
 * abstract class AbstractTemplateBuilder
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
abstract class AbstractTemplateBuilder extends AbstractTemplateInformation
{
    const XML_PATH_DEFAULT_BUSINESS_INFO = 'pdfinvoiceplus/business_info';

    const XML_PATH_DEFAULT_BUSINESS_CONTACT = 'pdfinvoiceplus/business_contact';

    const XML_PATH_DEFAULT_ADDITIONAL_INFO = 'pdfinvoiceplus/additional_info';

    const TEMPLATE_IMAGE_UPLOAD_DIR = 'magestore/pdfinvoice/logo/';

    /**
     * @var string
     */
    protected $_builderType = '';

    /**
     * @var string
     */
    protected $_pdfPageTitle = '';

    /**
     * @var \Magestore\Pdfinvoiceplus\Helper\Image
     */
    protected $_imageHelper;

    /**
     * @var \Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\TableItem
     */
    protected $_tableItemRenderer;

    /**
     * @var \Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\ShipmentTrack
     */
    protected $_trackingItemRenderer;

    /**
     * AbstractTemplateBuilder constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magestore\Pdfinvoiceplus\Model\Localization $localization
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Magestore\Pdfinvoiceplus\Helper\Image $imageHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magestore\Pdfinvoiceplus\Model\Localization $localization,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magestore\Pdfinvoiceplus\Helper\Image $imageHelper,
        array $data = []
    )
    {
        parent::__construct($context, $localization, $dataObjectFactory, $data);
        $this->_imageHelper = $imageHelper;
    }

    /**
     * @return string
     */
    protected function fileGetContents($url)
    {
        $ch = curl_init();
        $timeout = 5; // set to zero for no timeout
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $file_contents = curl_exec($ch);
        curl_close($ch);
        // display file
        return $file_contents;
    }

    /**
     * @return string
     */
    public function getPdfPageTitle()
    {
        return ucfirst($this->getBuilderType());
    }

    /**
     * @param string $builderType
     *
     * @return AbstractTemplateBuilder
     */
    public function setBuilderType($builderType)
    {
        $this->_builderType = $builderType;

        return $this;
    }

    /**
     * @return string
     */
    public function getBuilderType()
    {
        return $this->_builderType;
    }

    /**
     * @param TemplateBuilder\TableItem $tableItemRenderer
     *
     * @return AbstractTemplateBuilder
     */
    public function setTableItemRenderer($tableItemRenderer)
    {
        $this->_tableItemRenderer = $tableItemRenderer;

        return $this;
    }

    /**
     * @return TemplateBuilder\TableItem
     */
    public function getTableItemRenderer()
    {
        return $this->_tableItemRenderer;
    }

    /**
     * @return TemplateBuilder\ShipmentTrack
     */
    public function getTrackingItemRenderer()
    {
        return $this->_trackingItemRenderer;
    }

    /**
     * @param TemplateBuilder\ShipmentTrack $trackingItemRenderer
     */
    public function setTrackingItemRenderer($trackingItemRenderer)
    {
        $this->_trackingItemRenderer = $trackingItemRenderer;
    }


    protected function _construct()
    {
        parent::_construct();

        if ($this->hasData('builder_type')) {
            $this->setBuilderType($this->getData('builder_type'));
        }

        if ($this->hasData('tableitem_renderer')) {
            $this->setTableItemRenderer($this->getData('tableitem_renderer'));
        }
    }

    /**
     * @return string
     */
    public function getTitleWidth()
    {
        $pageFormat = $this->getPdfTemplateObject()->getData('format');
        $orientation = $this->getPdfTemplateObject()->getData('orientation');

        if ($orientation == PageOrientation::PAGE_PORTRAIT) {
            return ($pageFormat == 'A5') ? '83%' : '90%';
        } elseif ($orientation == PageOrientation::PAGE_LANDSCAPE) {
            return ($pageFormat == 'A5') ? '88.5%' : '93%';
        }
    }

    /**
     * @param $entityType
     */
//    public function getDefaultTemplateLoaderPath()
//    {
//        return sprintf(
//            "Magestore_Pdfinvoiceplus::default-template/%s/template-loader.phtml",
//            $this->getPdfTemplateObject()->getData('template_code')
//        );
//    }

    /**
     * @return string
     */
    public function getDefaultTemplateTableItems()
    {
        return sprintf(
            "Magestore_Pdfinvoiceplus::default-template/%s/table-item.phtml",
            $this->getPdfTemplateObject()->getData('template_code')
        );
    }

    /**
     * @param $url
     *
     * @return string
     */
    public function convertImage($url)
    {
        $type = pathinfo($url, PATHINFO_EXTENSION);
        $data = $this->fileGetContents($url);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        return $base64;
    }

    /**
     * @return string
     */
    public function getConvertedCompanyLogo()
    {
        $companyLogo = $this->getBusinessInfo()->getData('company_logo');

        return $this->convertImage(
            $this->_imageHelper->getMediaUrlImage(self::TEMPLATE_IMAGE_UPLOAD_DIR . $companyLogo)
        );
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $this->setTemplate($this->getDefaultTemplateLoaderPath());
        $this->_localization->setLocale($this->getPdfTemplateObject()->getData('localization'));
        $this->_localization->loadData();

        return parent::_toHtml();
    }

    /**
     * @return bool
     */
    public function isShowBarcode()
    {
        return $this->getPdfTemplateObject()->getData('barcode') == ShowBarcode::STATUS_YES
        && $this->getPdfTemplateObject()->getData('barcode_order');
    }

    /**
     * Retrieve source file of a view file
     *
     * @param string $fileId
     * @param array $params
     *
     * @return string
     */
    public function getViewFilePath($fileId, array $params = [])
    {
        try {
            $params = array_merge(['_secure' => $this->getRequest()->isSecure()], $params);
            $asset = $this->_assetRepo->createAsset($fileId, $params);

            return $asset->getSourceFile();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_logger->critical($e);

            return $this->_getNotFoundUrl();
        }
    }

    /**
     * @return string
     */
    public function getBarcodeImageFilename()
    {
        switch ($this->getPdfTemplateObject()->getData('barcode_type')) {
            case BarcodeType::BARCODE_POSTNET:
            case BarcodeType::BARCODE_PLANET:
                return 'POSTNET.jpg';
            case BarcodeType::BARCODE_IMB:
            case BarcodeType::BARCODE_RM4SCC:
            case BarcodeType::BARCODE_KIX:
                return 'IMB.jpg';
            case BarcodeType::BARCODE_EAN128A:
            case BarcodeType::BARCODE_C93:
            case BarcodeType::BARCODE_MSI:
            case BarcodeType::BARCODE_CODABAR:
            case BarcodeType::BARCODE_CODE11:
            case BarcodeType::BARCODE_C39:
            case BarcodeType::BARCODE_S25:
            case BarcodeType::BARCODE_C128A:
                return 'EAN128A.jpg';
            default:
                return $this->getPdfTemplateObject()->getData('barcode_type') . '.jpg';
        }
    }

    /**
     * @return string
     */
    public function getBarcodeImageUrl($fileName)
    {
        return $this->getViewFileUrl('Magestore_Pdfinvoiceplus::images/barcode/' . $fileName);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBarcodeStyleHtml()
    {
        /** @var \Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\BarcodeStyle $barcodeStyle */
        $barcodeStyle = $this->getLayout()
            ->createBlock('Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\BarcodeStyle');

        return $barcodeStyle->toHtml();
    }

    /**
     * @param string $tempalteCode
     * @param string $fileName
     *
     * @return string
     */
    public function getCssFilePath($fileName = '')
    {
        return $this->getViewFilePath(
            "Magestore_Pdfinvoiceplus::css/default-template/{$this->getPdfTemplateObject()->getTemplateCode()}/{$fileName}"
        );
    }

    /**
     * @return mixed
     */
    public function getTemplateCode()
    {
        return $this->getPdfTemplateObject()->getTemplateCode();
    }

    /**
     * @return string
     */
    public function getDefaultCssContent()
    {
        return $this->fileGetContents(
            $this->getViewFileUrl("Magestore_Pdfinvoiceplus::css/default-template/default.css")
        );
    }

    /**
     * @return string
     */
    public function getBarcodeCssContent()
    {
        return $this->fileGetContents(
            $this->getViewFileUrl("Magestore_Pdfinvoiceplus::css/barcode.css")
        );
    }

    /**
     * @return string
     */
    public function getCssContents()
    {
        return $this->fileGetContents(
            $this->getViewFileUrl("Magestore_Pdfinvoiceplus::css/default-template/{$this->getTemplateCode()}/style.css")
        );
    }

    /**
     * @param $variableId
     *
     * @return string
     */
    public function bindVariableName($variableId)
    {
        return "{{var {$this->getBuilderType()}_{$variableId}}}";
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTableItemsHtml()
    {
        if (!$this->getTableItemRenderer()) {
            /** @var \Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\TableItem $tableItemRenderer */
            $tableItemRenderer = $this->getLayout()
                ->createBlock('Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\TableItem');
            $tableItemRenderer->setPdfTemplateObject($this->getPdfTemplateObject());
            $this->setTableItemRenderer($tableItemRenderer);
        }

        return $this->getTableItemRenderer()->toHtml();
    }

    public function getTrackingTableHtml()
    {
        if (!$this->getTrackingItemRenderer()) {
            /** @var \Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\ShipmentTrack $trackingItemsRenderer */
            $trackingItemsRenderer = $this->getLayout()
                ->createBlock('Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\ShipmentTrack');
            $trackingItemsRenderer->setPdfTemplateObject($this->getPdfTemplateObject());
            $this->setTrackingItemRenderer($trackingItemsRenderer);
        }

        return $this->getTrackingItemRenderer()->toHtml();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBoxInformationsHtml()
    {
        /** @var \Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\BoxInformations $block */
        $block = $this->getLayout()
            ->createBlock('Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\BoxInformations');
        $block->setPdfTemplateObject($this->getPdfTemplateObject());

        return $block->toHtml();
    }

    /**
     * @return mixed
     */
    abstract public function getBarcode();

    /**
     * @return mixed
     */
    abstract public function getBindedStatus();

    /**
     * @param $entityType
     */
    abstract public function getDefaultTemplateLoaderPath();
}