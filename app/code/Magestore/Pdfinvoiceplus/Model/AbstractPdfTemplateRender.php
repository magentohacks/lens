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

use \Magestore\Pdfinvoiceplus\Model\PdfTemplate\Option\Statuses;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * class AbstractPdfTemplateRender
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
abstract class AbstractPdfTemplateRender extends \Magento\Framework\DataObject implements PdfTemplateRenderInterface
{
    /**
     *
     */
    const THE_START = '##productlist_start##';
    /**
     *
     */
    const THE_END = '##productlist_end##';
    /**
     *
     */
    const TRACKING_START = '##tracking_list_start##';
    /**
     *
     */
    const TRACKING_END = '##tracking_list_end##';
    /**
     *
     */
    const TRACK_TABLE_START = '##track_table_start##';
    /**
     *
     */
    const TRACK_TABLE_END = '##track_table_end##';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var
     */
    protected $_templateId;

    /**
     * @var
     */
    protected $_renderingEntity;

    /**
     * @var
     */
    protected $_templateHtml;

    /**
     * @var
     */
    protected $_processedHtml;

    /**
     * @var PdfTemplateRenderManager
     */
    protected $_pdfTemplateRenderManager;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogHelper;

    /**
     * @var \Magestore\Pdfinvoiceplus\Helper\Data
     */
    protected $_pdfHelper;

    /**
     * AbstractPdfTemplateRender constructor.
     * @param PdfTemplateRenderManager $pdfTemplateRenderManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     * @param \Magestore\Pdfinvoiceplus\Helper\Data $pdfHelper
     * @param array $data
     */
    public function __construct(
        \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderManager $pdfTemplateRenderManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Magestore\Pdfinvoiceplus\Helper\Data $pdfHelper,
        array $data = []
    )
    {
        parent::__construct($data);
        $this->_filesystem = $filesystem;
        $this->_objectManager = $objectManager;
        $this->_pdfTemplateRenderManager = $pdfTemplateRenderManager;
        $this->_catalogHelper = $catalogHelper;
        $this->_pdfHelper = $pdfHelper;

        $this->_contruct();
    }

    /**
     * @return $this
     */
    protected function _contruct()
    {
        if (!class_exists('mPDF', false)) {
            /** @var \Magento\Framework\Filesystem $filesystem */
            $filesystem = $this->_objectManager->get('Magento\Framework\Filesystem');

//            $classPath = $filesystem->getDirectoryWrite(DirectoryList::LIB_INTERNAL)
//                ->getAbsolutePath('mPdf/mpdf.php');
            $classPath = $this->_filesystem->getDirectoryWrite(DirectoryList::LIB_INTERNAL)
                ->getAbsolutePath('mPdf/autoload.php');

            require_once $classPath;
        }

        return $this;
    }

    /**
     * Render entity data to a html template
     *
     * @param \Magento\Sales\Model\AbstractModel $entity
     * @param                            $templateHtml
     *
     * @return mixed
     */
    public function render(\Magento\Sales\Model\AbstractModel $entity, $templateHtml)
    {
        $this->setRenderingEntity($entity);
        $this->setTemplateHtml($templateHtml);
        $this->setProcessedHtml($templateHtml);

        return $this->getProcessedHtml();
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    abstract public function getOrder();

    /**
     * @param mixed $templateHtml
     *
     * @return AbstractPdfTemplateRender
     */
    public function setTemplateHtml($templateHtml)
    {
        $this->_templateHtml = $templateHtml;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTemplateHtml()
    {
        return $this->_templateHtml;
    }

    /**
     * @param mixed $processedHtml
     *
     * @return AbstractPdfTemplateRender
     */
    public function setProcessedHtml($processedHtml)
    {
        $this->_processedHtml = $processedHtml;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProcessedHtml()
    {
        return $this->_processedHtml;
    }

    /**
     * @param $variablesData
     *
     * @return array
     */
    protected function _prepareVariablesData($variablesData)
    {
        $allKeysLabel = [];
        $allKeys = [];

        foreach (array_keys($variablesData) as $v) {
            if (isset($v) && isset($variablesData[$v]['value']) && isset($variablesData[$v]['label'])) {
                if (is_array($variablesData[$v]['label']) && is_array($variablesData[$v]['value'])) {
                    $allKeysLabel['label_' . $v] = $variablesData[$v]['value'] . ' ' . $variablesData[$v]['label'];
                }
            }
            if (isset($v) && isset($variablesData[$v]['value'])) {
                $allKeys[$v] = $variablesData[$v]['value'];
            }
        }

        return array_merge($allKeysLabel, $allKeys);
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getTemplate()
    {
        /** @var \Magestore\Pdfinvoiceplus\Model\ResourceModel\PdfTemplate\Collection $pdfTemplateCollection */
        $pdfTemplateCollection = $this->_objectManager->create('Magestore\Pdfinvoiceplus\Model\ResourceModel\PdfTemplate\Collection');

        if ($this->_templateId) {
            $pdfTemplateCollection->addFieldToFilter('template_id', ['eq' => $this->_templateId]);
        } else {
            $pdfTemplateCollection->addFieldToFilter('status', ['eq' => Statuses::STATUS_ACTIVE])->setOrder('created_at', 'DESC');
        }
        return $pdfTemplateCollection->getFirstItem();
    }

    /**
     * @param array $variables
     * @return mixed
     */
    public function processAllVars($variables = [])
    {
        $varData = [];
        foreach ($variables as $variable) {
            $varData[] = $this->_prepareVariablesData($variable);
        }
        $varsData = [];

        foreach ($varData as $value) {
            foreach ($value as $key => $val) {
                $varsData[$key] = $val;
            }
        }

        return $varsData;
    }

    /**
     * @param $productId
     * @return bool
     */
    public function isConfigurable($productId)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->_objectManager->create('Magento\Catalog\Model\Product');
        if ($product->load($productId)->getData('type_id') == 'configurable')
            return true;
    }

    /**
     * @param mixed $renderingEntity
     *
     * @return AbstractPdfTemplateRender
     */
    public function setRenderingEntity($renderingEntity)
    {
        $this->_renderingEntity = $renderingEntity;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRenderingEntity()
    {
        return $this->_renderingEntity;
    }

    /**
     * @return array
     */
    abstract public function getVariables();

    /**
     * @param string $templateText
     * @param array $variables
     *
     * @return mixed
     */
    public function mappingVariablesTemplate($templateText = '', array $variables = [])
    {
        if (empty($variables)) {
            return '';
        }

        $pdfProcessTemplate = $this->_objectManager->get('Magento\Email\Model\Template');

        return $pdfProcessTemplate->setTemplateText($templateText)
            ->getProcessedTemplate($variables);
    }
}