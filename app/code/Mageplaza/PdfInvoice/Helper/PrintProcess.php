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
 * @category   Mageplaza
 * @package    Mageplaza_PdfInvoice
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\PdfInvoice\Helper;

use Exception;
use horstoeko\zugferd\codelists\ZugferdElectronicAddressScheme;
use horstoeko\zugferd\codelists\ZugferdInvoiceType;
use horstoeko\zugferd\codelists\ZugferdVatCategoryCodes;
use horstoeko\zugferd\codelists\ZugferdVatTypeCodes;
use horstoeko\zugferd\ZugferdDocumentBuilder;
use horstoeko\zugferd\ZugferdDocumentPdfMerger;
use horstoeko\zugferd\ZugferdKositValidator;
use finfo;
use Magento\Customer\Model\CustomerFactory;
use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\State;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Shipment;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\PdfInvoice\Helper\Data as HelperData;
use Mageplaza\PdfInvoice\Model\CustomFunction;
use Mageplaza\PdfInvoice\Model\Source\Type;
use Mageplaza\PdfInvoice\Model\Template\Processor;
use Mageplaza\PdfInvoice\Model\TemplateFactory;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

/**
 * Class PrintProcess
 * @package Mageplaza\PdfInvoice\Helper
 */
class PrintProcess extends AbstractData
{
    public const MAGEPLAZA_DIR = 'var/mageplaza';

    /**
     * Module registry
     *
     * @var ComponentRegistrarInterface
     */
    private $componentRegistrar;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var string
     */
    protected $templateStyles = '';

    /**
     * @var TemplateFactory
     */
    protected $templateFactory;

    /**
     * @var $templateVars
     */
    protected $templateVars;

    /**
     * @var Processor
     */
    protected $templateProcessor;

    /**
     * @var string
     */
    protected $mode;

    /**
     * @var string
     */
    protected $fileName = 'PdfInvoice';

    /**
     * @var State
     */
    protected $state;

    /**
     * @var CustomFunction
     */
    protected $customFunction;

    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @var Renderer
     */
    protected $addressRenderer;

    /**
     * @var Creditmemo
     */
    protected $creditmemo;

    /**
     * @var CreditmemoRepositoryInterface
     */
    protected $creditmemoRepository;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var Invoice
     */
    protected $invoice;

    /**
     * @var InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * @var Shipment
     */
    protected $shipment;

    /**
     * @var ShipmentRepositoryInterface
     */
    protected $shipmentRepository;

    /**
     * @var string
     */
    protected $comment;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CountryFactory
     */
    private $_countryFactory;
    /**
     * @var Repository
     */
    protected $_assetRepo;

    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * @var array
     */
    protected $businessInfoCache = [];

    /**
     * @var array
     */
    protected $countryCache = [];

    /**
     * @var array
     */
    protected $regionCache = [];

    /**
     * PrintProcess constructor.
     *
     * @param Context $context
     * @param Order $order
     * @param State $state
     * @param Invoice $invoice
     * @param Shipment $shipment
     * @param Data $helperData
     * @param Filesystem $fileSystem
     * @param Creditmemo $creditmemo
     * @param Renderer $addressRenderer
     * @param PaymentHelper $paymentHelper
     * @param Processor $templateProcessor
     * @param DirectoryList $directoryList
     * @param CustomFunction $customFunction
     * @param TemplateFactory $templateFactory
     * @param StoreManagerInterface $storeManager
     * @param ObjectManagerInterface $objectManager
     * @param OrderRepositoryInterface $orderRepository
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param ComponentRegistrarInterface $componentRegistrar
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param CreditmemoRepositoryInterface $creditmemoRepository
     * @param CustomerFactory $customerFactory
     * @param CountryFactory $countryFactory
     * @param RegionFactory $regionFactory
     * @param Repository $assetRepo
     */
    public function __construct(
        Context $context,
        Order $order,
        State $state,
        Invoice $invoice,
        Shipment $shipment,
        HelperData $helperData,
        Filesystem $fileSystem,
        Creditmemo $creditmemo,
        Renderer $addressRenderer,
        PaymentHelper $paymentHelper,
        Processor $templateProcessor,
        DirectoryList $directoryList,
        CustomFunction $customFunction,
        TemplateFactory $templateFactory,
        StoreManagerInterface $storeManager,
        ObjectManagerInterface $objectManager,
        OrderRepositoryInterface $orderRepository,
        InvoiceRepositoryInterface $invoiceRepository,
        ComponentRegistrarInterface $componentRegistrar,
        ShipmentRepositoryInterface $shipmentRepository,
        CreditmemoRepositoryInterface $creditmemoRepository,
        CustomerFactory $customerFactory,
        CountryFactory $countryFactory,
        RegionFactory $regionFactory,
        Repository $assetRepo
    ) {
        $this->order                = $order;
        $this->state                = $state;
        $this->invoice              = $invoice;
        $this->shipment             = $shipment;
        $this->creditmemo           = $creditmemo;
        $this->helperData           = $helperData;
        $this->fileSystem           = $fileSystem;
        $this->paymentHelper        = $paymentHelper;
        $this->directoryList        = $directoryList;
        $this->customFunction       = $customFunction;
        $this->addressRenderer      = $addressRenderer;
        $this->templateFactory      = $templateFactory;
        $this->orderRepository      = $orderRepository;
        $this->invoiceRepository    = $invoiceRepository;
        $this->templateProcessor    = $templateProcessor;
        $this->componentRegistrar   = $componentRegistrar;
        $this->shipmentRepository   = $shipmentRepository;
        $this->creditmemoRepository = $creditmemoRepository;
        $this->customerFactory      = $customerFactory;
        $this->_countryFactory      = $countryFactory;
        $this->_assetRepo           = $assetRepo;
        $this->regionFactory        = $regionFactory;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * Check default template
     *
     * @param string|int $templateId
     *
     * @return bool
     */
    public function checkDefaultTemplate($templateId)
    {
        return array_key_exists($templateId, $this->helperData->getTemplatesConfig());
    }

    /**
     * @param string $templateType
     * @param string|int $templateId
     *
     * @return string
     * @throws FileSystemException
     */
    public function getDefaultTemplateHtml($templateType, $templateId)
    {
        return $this->readFile($this->getTemplatePath($templateType, $templateId));
    }

    /**
     * @param string $templateType
     * @param string|int $templateId
     *
     * @return string
     * @throws FileSystemException
     */
    public function getDefaultTemplateCss($templateType, $templateId)
    {
        return $this->readFile($this->getTemplatePath($templateType, $templateId, '.css'));
    }

    /**
     * Get default template path
     *
     * @param string $templateType
     * @param string|int $templateId
     * @param string $type
     *
     * @return string
     */
    public function getTemplatePath($templateType, $templateId, $type = '.html')
    {
        return $this->getBaseTemplatePath() . $templateType . '/' . $templateId . $type;
    }

    /**
     * @param string $relativePath
     *
     * @return string
     * @throws FileSystemException
     */
    public function readFile($relativePath)
    {
        $rootDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::ROOT);

        return $rootDirectory->readFile($relativePath);
    }

    /**
     * @param string|int $templateId
     * @param string $type
     *
     * @return string
     * @throws FileSystemException
     */
    public function getTemplateHtml($templateId, $type)
    {
        if ($this->checkDefaultTemplate($templateId)) {
            $this->templateStyles = $this->getDefaultTemplateCss($type, $templateId);

            return $this->getDefaultTemplateHtml($type, $templateId);
        }

        $templateModel = $this->templateFactory->create();
        $template      = $templateModel->load($templateId);
        if ($template->getId()) {
            $this->templateStyles = $template->getTemplateStyles();

            return $template->getTemplateHtml();
        }
    }

    /**
     * Process pdf template for each type
     *
     * @param string $type
     * @param array $templateVars
     * @param int $storeId
     * @param Order $object
     *
     * @return string
     * @throws FileSystemException
     * @throws MpdfException
     */
    public function processPDFTemplate($type, $templateVars, $storeId, $object)
    {
        $orderId = $object->getId() ?: $object->getOrderId();
        if ($this->helperData->isEnableAttachment($type, $storeId)
            && $this->isAllowCustomerGroup($type, $object, $storeId)
            && $this->validateCondition($type, $orderId)
        ) {
            $templateId                    = $this->helperData->getPdfTemplate($type, $storeId);
            $templateHtml                  = $this->getTemplateHtml($templateId, $type);
            $templateVars[$type . 'Note']  = $this->helperData->getPdfNote($type, $storeId);
            $order                         = $templateVars['order'];
            $imgHeaderUrl                  = $this->getHeaderImgUrl();
            $templateVars['header4']       = $imgHeaderUrl->getData('header4');
            $templateVars['header5']       = $imgHeaderUrl->getData('header5');
            $templateVars['customer_name'] = $order->getCustomerName();
            $templateVars['payment_html']  = $this->getPaymentHtml($order, $storeId);
            $templateVars['order_id']      = $order->getId();
            if ($type !== 'order') {
                $templateVars[$type . '_id'] = $templateVars[$type]->getId();
            }
            $templateVars[$type . '_date'] = $this->customFunction->formatDate($templateVars[$type]->getData('created_at'));
            if ($type === 'shipment') {
                $templateVars['shipment_amount'] = $order->formatPriceTxt($order->getShippingAmount());
            } else {
                $templateVars[$type . '_amount'] = $order->formatPriceTxt($templateVars[$type]->getGrandTotal());
            }

            return $this->getPDFContent($templateHtml, $templateVars, 'S', $storeId);
        }

        return '';
    }

    /**
     * Set template vars
     *
     * @param array $data
     *
     * @return mixed
     */
    public function setTemplateVars($data)
    {
        return $this->templateVars = $data;
    }

    /**
     * Get store id
     *
     * @return mixed
     */
    public function getStoreId()
    {
        $store = $this->templateVars['store'];

        return $store->getId();
    }

    /**
     * Get PDF Content
     *
     * @param string $html
     * @param array $templateVars
     * @param string $dest
     * @param null $storeId
     *
     * @return string
     * @throws MpdfException
     */
    public function getPDFContent($html, $templateVars, $dest = 'S', $storeId = null)
    {
        $processor = $this->templateProcessor->setVariable(
            $this->addCustomTemplateVars($templateVars, $storeId)
        );
        $processor->setTemplateHtml($html . '<style>' . $this->templateStyles . '</style>');
        $processor->setStore($storeId);

        $html = $processor->processTemplate();

        if ($this->getMode() === 'prints') {
            return $html;
        }

        if ($this->helperData->isEnableEInvoice($storeId) && isset($templateVars['invoice_id'])) {
            $invoice = $templateVars['invoice'];

            return $this->exportEInvoicePDF($this->fileName . '.pdf', $invoice, $html, $storeId, $dest);
        }

        return $this->exportToPDF($this->fileName . '.pdf', $html, $storeId, $dest);
    }


    /**
     * @param $fileName
     * @param $invoice
     * @param $html
     * @param $storeId
     * @param $dest
     *
     * @return string|void
     * @throws Exception
     */
    public function exportEInvoicePDF($fileName, $invoice, $html, $storeId, $dest = 'S')
    {
        try {
            $xmlData = $this->generateXml($invoice, $storeId);
            $pdfData = $this->exportToPDF($fileName, $html, $storeId, 'S');

            if (!$xmlData || !$pdfData) {
                throw new Exception('XML data generation failed');
            }
            $pdfMerge = new ZugferdDocumentPdfMerger($xmlData, $pdfData);
            $pdfMerge->generateDocument();

            $filePath = BP . '/' . self::MAGEPLAZA_DIR . '/pdfinvoice/' . $fileName;
            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }
            if ($dest === 'S') {
                return $pdfMerge->downloadString();
            } elseif ($dest === 'D') {
                $pdfMerge->saveDocument($filePath);
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
                header('Content-Length: ' . filesize($filePath));
                readfile($filePath);
                unlink($filePath);
            }
        } catch (Exception $e) {
            throw new Exception(__($e->getMessage()));
        }
    }

    /**
     * @param $invoice
     * @param $storeId
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function generateXml($invoice, $storeId)
    {
        $profile         = $this->helperData->getEInvoiceProfile($storeId);
        $documentBuilder = ZugferdDocumentBuilder::createNew($profile);
        $order           = $invoice->getOrder();
        $this->setDocumentBasicInfo($documentBuilder, $invoice);
        $this->setDocumentSellerInfo($documentBuilder, $storeId);
        $this->setDocumentBuyerInfo($documentBuilder, $order);
        $totals = $this->processInvoiceItems($documentBuilder, $invoice);
        $this->addShippingToDocument($documentBuilder, $invoice, $order, $totals);
        $this->addDocumentTaxes($documentBuilder, $totals['taxAdded']);
        $this->addDiscountToDocument($documentBuilder, $invoice);
        $this->setDocumentSummation($documentBuilder, $invoice, $totals, $storeId);

        return $this->validateDocument($documentBuilder) ? $documentBuilder->getContent() : '';
    }


    /**
     * Set basic document information
     *
     * @param $documentBuilder
     * @param $invoice
     */
    private function setDocumentBasicInfo($documentBuilder, $invoice)
    {
        $documentBuilder->setDocumentInformation(
            $invoice->getIncrementId(),
            ZugferdInvoiceType::INVOICE,
            \DateTime::createFromFormat('Y-m-d H:i:s', $invoice->getCreatedAt()),
            $invoice->getOrderCurrencyCode()
        );
    }

    /**
     * Set seller information
     *
     * @param $documentBuilder
     * @param $storeId
     *
     * @throws NoSuchEntityException
     */
    private function setDocumentSellerInfo($documentBuilder, $storeId)
    {
        $store        = $this->storeManager->getStore($storeId);
        $businessInfo = $this->getBusinessInformation($storeId);

        $legalInfo = $businessInfo->getData('legal_information');
        $description = trim($legalInfo ? $legalInfo : '');

        $documentBuilder->setDocumentSeller(
            $businessInfo->getData('company') ?: $store->getName(),
            $businessInfo->getData('seller_id'),
            !empty($description) ? $description : null
        );

        if ($businessInfo->getData('trade_name') || $businessInfo->getData('trade_id')) {
            $documentBuilder->setDocumentSellerLegalOrganisation(
                $businessInfo->getData('trade_id'),
                '0088',
                $businessInfo->getData('trade_name')
            );
        }

        if ($businessInfo->getData('seller_id')) {
            $documentBuilder->addDocumentSellerGlobalId(
                $businessInfo->getData('seller_id'),
                '0088'
            );
        }

        if ($businessInfo->getData('vat_number')) {
            $documentBuilder->addDocumentSellerVATRegistrationNumber($businessInfo->getData('vat_number'));
        }

        if ($businessInfo->getData('tax_number')) {
            $documentBuilder->addDocumentSellerTaxRegistration('FC', $businessInfo->getData('tax_number'));
        }

        if ($businessInfo->getData('weee_number')) {
            $documentBuilder->addDocumentSellerTaxRegistration('WEEE', $businessInfo->getData('weee_number'));
        }

        $documentBuilder->setDocumentSellerAddress(
            $businessInfo->getData('street_line1'),
            $businessInfo->getData('street_line2'),
            null,
            $businessInfo->getData('postcode'),
            $businessInfo->getData('city'),
            $businessInfo->getData('country_id')
        );

        if ($businessInfo->getData('contact_email')) {
            $documentBuilder->setDocumentSellerCommunication(
                ZugferdElectronicAddressScheme::UNECE3155_EM,
                $businessInfo->getData('contact_email')
            );
        }

        $documentBuilder->setDocumentSellerContact(
            $businessInfo->getData('contact_name'),
            $businessInfo->getData('company'),
            $businessInfo->getData('contact_phone'),
            $businessInfo->getData('fax') ?: '',
            $businessInfo->getData('contact_email')
        );
    }

    /**
     * Set buyer information
     *
     * @param $documentBuilder
     * @param $order
     */
    private function setDocumentBuyerInfo($documentBuilder, $order)
    {
        $billingAddress = $order->getBillingAddress();
        $documentBuilder->setDocumentBuyer(
            $billingAddress->getFirstname() . ' ' . $billingAddress->getLastname(),
            null,
            null
        );
        $documentBuilder->setDocumentBuyerAddress(
            $billingAddress->getStreetLine(1),
            $billingAddress->getStreetLine(2),
            null,
            $billingAddress->getPostcode(),
            $billingAddress->getCity(),
            $billingAddress->getCountryId()
        );

        $shippingAddress = $order->getShippingAddress();
        if ($shippingAddress) {
            $documentBuilder->setDocumentShipTo(
                $shippingAddress->getFirstname() . ' ' . $shippingAddress->getLastname()
            );
            $documentBuilder->setDocumentShipToAddress(
                $shippingAddress->getStreetLine(1),
                $shippingAddress->getStreetLine(2),
                null,
                $shippingAddress->getPostcode(),
                $shippingAddress->getCity(),
                $shippingAddress->getCountryId()
            );
        }
    }

    /**
     *
     * @param $invoice
     *
     * @return array
     */
    private function preprocessInvoiceItems($invoice)
    {
        $validItems = [];
        $allItems   = $invoice->getAllItems();

        foreach ($allItems as $item) {
            if (!$item->getRowTotal()) {
                continue;
            }

            $orderItem   = $item->getOrderItem();
            $productType = $orderItem->getProductType();

            if ($this->shouldIncludeItemInZugferd($item, $productType)) {
                $validItems[] = [
                    'item'        => $item,
                    'orderItem'   => $orderItem,
                    'productType' => $productType,
                    'taxPercent'  => $orderItem->getTaxPercent()
                ];
            }
        }

        return $validItems;
    }

    /**
     *
     * @param $documentBuilder
     * @param $invoice
     *
     * @return array
     */
    private function processInvoiceItems($documentBuilder, $invoice)
    {
        $validItems = $this->preprocessInvoiceItems($invoice);

        $line                 = 1;
        $invoiceTotal         = 0;
        $invoiceTotalsWithTax = 0;
        $totalZAmount         = 0;
        $taxAdded             = [];

        foreach ($validItems as $itemData) {
            $item        = $itemData['item'];
            $productType = $itemData['productType'];
            $taxPercent  = $itemData['taxPercent'];

            $this->addItemToDocument($documentBuilder, $item, $productType, $invoice, $line);

            $categoryCode = $taxPercent > 0 ? ZugferdVatCategoryCodes::STAN_RATE : ZugferdVatCategoryCodes::ZERO_RATE_GOOD;

            $invoiceTotal         += $item->getRowTotal();
            $invoiceTotalsWithTax += $item->getRowTotal() + $item->getTaxAmount();

            if ($categoryCode === ZugferdVatCategoryCodes::ZERO_RATE_GOOD) {
                $totalZAmount += $item->getRowTotal();
            }

            $taxKey = $categoryCode . '_' . number_format($taxPercent, 2);
            if (!isset($taxAdded[$taxKey])) {
                $taxAdded[$taxKey] = [
                    'category'         => $categoryCode,
                    'base'             => 0,
                    'tax'              => 0,
                    'rate'             => $taxPercent,
                    'exemption_reason' => null
                ];
            }
            $taxAdded[$taxKey]['base'] += $item->getRowTotal();
            $taxAdded[$taxKey]['tax']  += $item->getTaxAmount();

            $line++;
        }

        return [
            'line'                 => $line,
            'invoiceTotal'         => $invoiceTotal,
            'invoiceTotalsWithTax' => $invoiceTotalsWithTax,
            'totalZAmount'         => $totalZAmount,
            'taxAdded'             => $taxAdded
        ];
    }

    /**
     * Add item to ZUGFeRD document
     *
     * @param $documentBuilder
     * @param $item
     * @param $productType
     * @param $invoice
     * @param $line
     */
    private function addItemToDocument($documentBuilder, $item, $productType, $invoice, $line)
    {
        $documentBuilder->addNewPosition($line);

        $productName        = $item->getName();
        $productDescription = $this->getZugferdProductDescription($item, $productType, $invoice);
        $productSku         = $item->getSku();

        $documentBuilder->setDocumentPositionProductDetails($productName, $productDescription, $productSku);

        $quantity = $this->getZugferdProductQuantity($item, $productType);
        $documentBuilder->setDocumentPositionQuantity($quantity, 'C62');
        $documentBuilder->setDocumentPositionNetPrice($item->getPrice());

        $taxPercent   = $item->getOrderItem()->getTaxPercent();
        $categoryCode = $taxPercent > 0 ? ZugferdVatCategoryCodes::STAN_RATE : ZugferdVatCategoryCodes::ZERO_RATE_GOOD;

        $documentBuilder->addDocumentPositionTax(
            $categoryCode,
            ZugferdVatTypeCodes::VALUE_ADDED_TAX,
            $taxPercent
        );

        $documentBuilder->setDocumentPositionLineSummation($item->getRowTotal());
    }

    /**
     * Add taxes to document
     *
     * @param $documentBuilder
     * @param $taxAdded
     */
    private function addDocumentTaxes($documentBuilder, $taxAdded)
    {
        foreach ($taxAdded as $taxData) {
            if (isset($taxData['category'])) {
                $documentBuilder->addDocumentTax(
                    $taxData['category'],
                    ZugferdVatTypeCodes::VALUE_ADDED_TAX,
                    $taxData['base'],
                    $taxData['tax'],
                    $taxData['rate'],
                    $taxData['exemption_reason'] ?? null
                );
            }
        }
    }

    /**
     * Add shipping to document if applicable
     *
     * @param $documentBuilder
     * @param $invoice
     * @param $order
     * @param $totals
     */
    private function addShippingToDocument($documentBuilder, $invoice, $order, &$totals)
    {
        if ($invoice->getShippingAmount() && !$this->isInvoiceOnlyVirtualDownloadable($invoice)) {
            $documentBuilder->addNewPosition($totals['line']);
            $documentBuilder->setDocumentPositionProductDetails($order->getShippingDescription(), __('Shipping Fee'));
            $documentBuilder->setDocumentPositionQuantity(1, 'C62');
            $documentBuilder->setDocumentPositionNetPrice($invoice->getShippingAmount());

            $shippingTaxPercent = $invoice->getShippingTaxAmount() > 0
                ? ($invoice->getShippingTaxAmount() / $invoice->getShippingAmount()) * 100
                : 0;

            $shippingCategory = $shippingTaxPercent > 0
                ? ZugferdVatCategoryCodes::STAN_RATE
                : ZugferdVatCategoryCodes::EXEM_FROM_TAX;

            $documentBuilder->addDocumentPositionTax(
                $shippingCategory,
                ZugferdVatTypeCodes::VALUE_ADDED_TAX,
                $shippingTaxPercent,
                null,
                'Shipping'
            );

            $taxKey = $shippingCategory . '_' . number_format($shippingTaxPercent, 2);
            if (!isset($totals['taxAdded'][$taxKey])) {
                $totals['taxAdded'][$taxKey] = [
                    'category'         => $shippingCategory,
                    'base'             => 0,
                    'tax'              => 0,
                    'rate'             => $shippingTaxPercent,
                    'exemption_reason' => $shippingCategory === ZugferdVatCategoryCodes::EXEM_FROM_TAX ? 'Shipping services' : null
                ];
            }
            $totals['taxAdded'][$taxKey]['base'] += $invoice->getShippingAmount();
            $totals['taxAdded'][$taxKey]['tax']  += $invoice->getShippingTaxAmount();

            $documentBuilder->setDocumentPositionLineSummation($invoice->getShippingAmount());

            $totals['invoiceTotal']         += $invoice->getShippingAmount();
            $totals['invoiceTotalsWithTax'] += $invoice->getShippingAmount() + $invoice->getShippingTaxAmount();
            $totals['line']++;
        }
    }

    /**
     * Add discount to document if applicable
     *
     * @param $documentBuilder
     * @param $invoice
     */
    private function addDiscountToDocument($documentBuilder, $invoice)
    {
        if ($invoice->getDiscountAmount() > 0) {
            $documentBuilder->addDocumentAllowanceCharge(
                -1 * $invoice->getDiscountAmount(),
                false,
                ZugferdVatCategoryCodes::EXEM_FROM_TAX,
                ZugferdVatTypeCodes::VALUE_ADDED_TAX,
                0,
                null,
                null,
                null,
                null,
                null,
                $invoice->getDiscountDescription() ?: __('Discount')
            );
        }
    }

    /**
     * Set document summation
     *
     * @param $documentBuilder
     * @param $invoice
     * @param $totals
     * @param $storeId
     */
    private function setDocumentSummation($documentBuilder, $invoice, $totals, $storeId)
    {
        $documentBuilder->addDocumentPaymentTerm($this->helperData->getPaymentTerms($storeId) ?: '');

        $calculatedTotalTax = 0;
        foreach ($totals['taxAdded'] as $taxData) {
            $calculatedTotalTax += $taxData['tax'];
        }

        $totalWithoutVat = $totals['invoiceTotal'] - abs($invoice->getDiscountAmount());

        $calculatedGrandTotal = $totalWithoutVat + $calculatedTotalTax;

        $documentBuilder->setDocumentSummation(
            $calculatedGrandTotal,
            $calculatedGrandTotal,
            $totalWithoutVat,
            0,
            abs($invoice->getDiscountAmount()),
            $totalWithoutVat,
            $calculatedTotalTax
        );
    }

    /**
     * @param $documentBuilder
     *
     * @return bool
     */
    public function validateDocument($documentBuilder)
    {
        return true;
        /* Uncomment this to enable ZUGFeRD validation to help debug issues with the generated XML */
        /*
        try {
            $kositValidator = new ZugferdKositValidator($documentBuilder);
            $kositValidator->disableCleanup();
            $kositValidator->validate();

            var_dump($kositValidator->getValidationErrors());
        } catch (\Exception $e) {
            $this->_logger->warning('ZUGFeRD validation failed: ' . $e->getMessage());

            return true;
        }
        */
    }

    /**
     * Set mode print
     *
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * Get mode print
     *
     * @return mixed
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param $type
     * @param $ids
     *
     * @return void
     */
    public function printAllPdf($type, $ids)
    {
        if (!is_array($ids)) {
            return;
        }

        if (count($ids) === 1) {
            $this->processSingleStorePdf($type, $ids);
        } else {
            $this->processMultipleStoresPdf($type, $ids);
        }
    }

    /**
     * @return string[]
     */
    protected function createDirectory()
    {
        $baseDir = BP . '/' . self::MAGEPLAZA_DIR;
        $pdfDir  = $baseDir . '/pdfinvoice';
        $tmpDir  = $baseDir . '/tmp';

        foreach ([$baseDir, $pdfDir, $tmpDir] as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        return [$baseDir, $pdfDir, $tmpDir];
    }

    /**
     * @param $sid
     * @param $item
     * @param $type
     *
     * @return void
     * @throws LocalizedException
     */
    private function processEInvoices($sid, $item, $type)
    {
        [$baseDir, $pdfDir, $tmpDir] = $this->createDirectory();
        $filesToZip = [];

        foreach ($item as $invoiceId) {
            try {
                $invoice = $this->invoiceRepository->get($invoiceId);
                if (!$invoice || !$invoice->getEntityId()) {
                    $this->_logger->warning("Invoice ID {$invoiceId} not found");
                    continue;
                }

                $fileName     = $this->getFileName($type, $sid, $invoice->getIncrementId());
                $data         = $this->getDataProcess($type, $invoiceId);
                $templateHtml = $this->getTemplateHtml($this->helperData->getPdfTemplate(Type::INVOICE, $sid), $type);

                $html    = $this->processTemplateHtml($templateHtml, $data, $sid);
                $xmlData = $this->generateXml($invoice, $sid);
                $pdfData = $this->exportToPDF($fileName, $html, $sid, 'S');

                if (!$xmlData || !$pdfData) {
                    throw new Exception('Failed to generate XML or PDF data for invoice #' . $invoice->getIncrementId());
                }

                $outputFilePath = $pdfDir . '/' . $sid . '-' . $fileName . '.pdf';
                $this->mergeAndSavePdf($xmlData, $pdfData, $outputFilePath);
                $filesToZip[] = $outputFilePath;
            } catch (Exception $e) {
                $this->_logger->critical('Error generating e-invoice for ID ' . $invoiceId . ': ' . $e->getMessage());
            }
        }

        $this->handleGeneratedFiles($filesToZip);
    }

    /**
     * @param $sid
     * @param $item
     * @param $type
     *
     * @return void
     */
    private function processEInvoicesBatch($sid, $item, $type)
    {
        foreach ($item as $invoiceId) {
            try {
                $invoice = $this->invoiceRepository->get($invoiceId);
                if (!$invoice || !$invoice->getEntityId()) {
                    $this->_logger->warning("Invoice ID {$invoiceId} not found");
                    continue;
                }

                $fileName     = $this->getFileName($type, $sid, $invoice->getIncrementId());
                $data         = $this->getDataProcess($type, $invoiceId);
                $templateHtml = $this->getTemplateHtml($this->helperData->getPdfTemplate(Type::INVOICE, $sid), $type);

                $html    = $this->processTemplateHtml($templateHtml, $data, $sid);
                $xmlData = $this->generateXml($invoice, $sid);
                $pdfData = $this->exportToPDF($fileName, $html, $sid, 'S');

                if (!$xmlData || !$pdfData) {
                    throw new Exception('Failed to generate XML or PDF data for invoice #' . $invoice->getIncrementId());
                }

                $outputFilePath = BP . '/' . self::MAGEPLAZA_DIR . '/pdfinvoice/' . $sid . '-' . $fileName . '.pdf';
                $this->mergeAndSavePdf($xmlData, $pdfData, $outputFilePath);
            } catch (Exception $e) {
                $this->_logger->critical('Error generating e-invoice for ID ' . $invoiceId . ': ' . $e->getMessage());
            }
        }
    }

    /**
     * @param $type
     * @param $ids
     *
     * @return void
     * @throws LocalizedException
     * @throws MpdfException
     */
    private function processSingleStorePdf($type, $ids)
    {
        $item        = reset($ids);
        $sid         = key($ids);
        $fileNamePdf = $this->getFileNameWithDate($type);

        if ($type == Type::INVOICE && $this->helperData->isEnableEInvoice($sid)) {
            $this->processEInvoices($sid, $item, $type);
        } else {
            $this->getMpdfContent($sid, $item, $type)->Output($fileNamePdf, 'D');
        }
    }

    /**
     * @param $type
     * @param $ids
     *
     * @return void
     * @throws LocalizedException
     * @throws MpdfException
     */
    private function processMultipleStoresPdf($type, $ids)
    {
        $this->createDirectory();
        foreach ($ids as $sid => $item) {
            $fileNamePdf = $this->getFileNameWithDate($type);

            if ($type == Type::INVOICE && $this->helperData->isEnableEInvoice($sid)) {
                $this->processEInvoicesBatch($sid, $item, $type);
            } else {
                $this->getMpdfContent($sid, $item, $type)->Output(
                    BP . '/' . self::MAGEPLAZA_DIR . '/pdfinvoice/' . $sid . '-' . $fileNamePdf,
                    'F'
                );
            }
        }
        $this->downloadFile($this->packFile());
    }

    /**
     * @param $type
     * @param $sid
     * @param $incrementId
     *
     * @return array|mixed|string|string[]
     */
    private function getFileName($type, $sid, $incrementId)
    {
        $fileNameTemplate = $this->helperData->getFileName($type, $sid) ?: $type . '_%increment_id';

        return str_contains($fileNameTemplate, '%increment_id')
            ? str_replace('%increment_id', $incrementId, $fileNameTemplate)
            : $fileNameTemplate;
    }

    /**
     * @param $type
     *
     * @return string
     */
    private function getFileNameWithDate($type)
    {
        $genericName = $this->helperData->getGenericName($type);
        if (strpos($genericName, '%date') !== false) {
            return str_replace('%date', date('Y-m-d'), $genericName) . '.pdf';
        }

        return $genericName ? $genericName . '.pdf' : $type . 's' . date('Y-m-d') . '.pdf';
    }

    /**
     * @param $templateHtml
     * @param $data
     * @param $sid
     *
     * @return string
     */
    private function processTemplateHtml($templateHtml, $data, $sid)
    {
        $processor = $this->templateProcessor->setVariable($this->addCustomTemplateVars($data, $sid));
        $processor->setTemplateHtml($templateHtml . '<style>' . $this->templateStyles . '</style>');
        $processor->setStore($sid);

        return $processor->processTemplate();
    }

    /**
     * @param $xmlData
     * @param $pdfData
     * @param $outputFilePath
     *
     * @return void
     */
    private function mergeAndSavePdf($xmlData, $pdfData, $outputFilePath)
    {
        $pdfMerge = new ZugferdDocumentPdfMerger($xmlData, $pdfData);
        $pdfMerge->generateDocument();
        $pdfMerge->saveDocument($outputFilePath);
    }

    /**
     * @param $filesToZip
     *
     * @return void
     * @throws LocalizedException
     */
    private function handleGeneratedFiles($filesToZip)
    {
        if (count($filesToZip) > 1) {
            $this->downloadFile($this->packFile());
        } elseif (count($filesToZip) === 1) {
            $filePath = $filesToZip[0];
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            unlink($filePath);
        } else {
            throw new LocalizedException(__('No invoices could be generated.'));
        }
    }

    /**
     * Get Mpdf content
     *
     * @param int $sid
     * @param array $item
     * @param string $type
     *
     * @return Mpdf
     * @throws Exception
     * @throws LocalizedException
     * @throws MpdfException
     */
    public function getMpdfContent($sid, $item, $type)
    {
        $pageSize = $this->helperData->getPageSize($sid);
        $mpdf     = $this->createMpdf($pageSize);
        $this->setPageNumber($mpdf);
        $this->setMode('prints');
        foreach ($item as $id) {
            $html = $this->printPdf($type, $id);
            $mpdf->WriteHTML($html);
            if ($id !== end($item)) {
                $mpdf->addPage();
            }
        }

        return $mpdf;
    }

    /***
     * Export zip file to Frontend
     *
     * @param string $zipFile
     */
    public function downloadFile($zipFile)
    {
        if (file_exists($zipFile)) {
            header("Content-type: application/zip");
            header("Content-Disposition: attachment; filename=" . basename($zipFile));  // ← Use actual filename
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile($zipFile);
        }
    }

    /***
     * Pack to zip file and delete all file .pdf
     *
     * @return string
     */
    public function packFile()
    {
        // Get real path for our folder
        $rootPath = realpath(BP . '/' . self::MAGEPLAZA_DIR . DIRECTORY_SEPARATOR . 'pdfinvoice');
        $zipPath  = realpath(BP . '/' . self::MAGEPLAZA_DIR . DIRECTORY_SEPARATOR . 'tmp');

        // Initialize archive object
        $zip     = new ZipArchive();
        $genericName = $this->helperData->getGenericName(Type::INVOICE);
        if (strpos($genericName, '%date') !== false) {
            $fileName = str_replace('%date', date('Y-m-d'), $genericName) . '.zip';
        } else {
            $fileName = $genericName . '.zip';
        }
        $zipFile = $zipPath . DIRECTORY_SEPARATOR . $fileName;
        $zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $filesToDelete = [];

        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {
                // Get real and relative path for current file
                $filePath     = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);
                $zip->addFile($filePath, $relativePath);
                $filesToDelete[] = $filePath;
            }
        }

        $zip->close();

        // Delete all files from var/Mageplaza/PdfInvoice
        foreach ($filesToDelete as $file) {
            unlink($file);
        }

        return $zipFile;
    }

    /**
     * @param int $storeId
     *
     * @return int
     * @throws LocalizedException
     */
    public function checkStoreId($storeId)
    {
        if ($this->state->getAreaCode() === Area::AREA_FRONTEND) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        return $storeId;
    }

    /**
     * get invoice ids
     *
     * @param int $orderId
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getInvoiceIds($orderId)
    {
        $order = $this->order->load($orderId);
        $ids   = [];
        foreach ($order->getInvoiceCollection() as $invoice) {
            $currentStoreId         = $this->storeManager->getStore()->getId();
            $ids[$currentStoreId][] = $invoice->getId();
        }

        return $ids;
    }

    /**
     * Get Shipment ids
     *
     * @param int $orderId
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getShipmentIds($orderId)
    {
        $order = $this->order->load($orderId);
        $ids   = [];
        foreach ($order->getShipmentsCollection() as $shipment) {
            $currentStoreId         = $this->storeManager->getStore()->getId();
            $ids[$currentStoreId][] = $shipment->getId();
        }

        return $ids;
    }

    /**
     * Get credit memo ids
     *
     * @param int $orderId
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getCreditmemoIds($orderId)
    {
        $order = $this->order->load($orderId);
        $ids   = [];
        foreach ($order->getCreditmemosCollection() as $creditmemo) {
            $currentStoreId         = $this->storeManager->getStore()->getId();
            $ids[$currentStoreId][] = $creditmemo->getId();
        }

        return $ids;
    }

    /**
     * @param string $fileName
     * @param string $html
     * @param int $storeId
     * @param string $dest
     *
     * @return string
     * @throws MpdfException
     */
    public function exportToPDF($fileName, $html, $storeId, $dest)
    {
        $this->convertMagentoUrlsToPaths($html);
        $pageSize = $this->helperData->getPageSize($storeId) ?: 'A4';
        $mpdf     = $this->createMpdf($pageSize, $storeId);
        $this->setPageNumber($mpdf);
        $mpdf->WriteHTML($html);

        return $mpdf->Output($fileName, $dest);
    }

    /**
     * Convert magento urls to paths local
     *
     * @param string $html
     */
    public function convertMagentoUrlsToPaths(string &$html)
    {
        try {
            $store     = $this->storeManager->getStore();
            $baseUrl   = rtrim($this->_urlBuilder->getBaseUrl(), '/');
            $mediaUrl  = rtrim($this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]), '/');
            $staticUrl = rtrim($this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_STATIC]), '/');
        } catch (Exception $e) {
            return;
        }

        $pattern = '/(href|src)=["\'](https?:\/\/[^"\']+)["\']/i';

        $html = preg_replace_callback($pattern, function ($matches) use ($baseUrl, $mediaUrl, $staticUrl) {
            $url = $matches[2];

            if (str_starts_with($url, $mediaUrl)) {
                $path = str_replace($mediaUrl, BP . '/pub/media', $url);
            } elseif (str_starts_with($url, $staticUrl)) {
                $path = str_replace($staticUrl, BP . '/pub/static', $url);
            } elseif (str_starts_with($url, $baseUrl)) {
                $path = str_replace($baseUrl, '', $url);
            } else {
                return $matches[0];
            }

            return $matches[1] . '="' . $path . '"';
        }, $html);
    }

    /**
     * set Header or Footer
     *
     * @param Mpdf $mpdf
     * @param $storeId
     */
    public function setPageNumber($mpdf, $storeId = null)
    {
        $mpdf->SetAutoPageBreak(true, 5);
        if ($this->helperData->isEnablePageNumber($storeId)) {
            $page = 'Page {PAGENO} of {nb}&nbsp;&nbsp;&nbsp;&nbsp;';
            if ($this->helperData->getPositionOfPageNum() === 'bottom') {
                $mpdf->setFooter($page);
            } else {
                $mpdf->SetHeader($page);
            }
        }
    }

    /**
     * Create mpdf
     *
     * @param string $pageSize
     * @param null $storeId
     *
     * @return Mpdf
     * @throws MpdfException
     */
    public function createMpdf($pageSize, $storeId = null)
    {
        $config = $this->getMpdfConfig($pageSize, $storeId);

        return new Mpdf($config);
    }

    /**
     * Get MPDF configuration
     *
     * @param string $pageSize
     * @param null $storeId
     *
     * @return array
     */
    private function getMpdfConfig($pageSize, $storeId = null)
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs      = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData          = $defaultFontConfig['fontdata'];
        unset($fontData['dejavusanscondensed']);

        $pdfMargin = $this->helperData->getPdfMargin(null, $storeId);

        return [
            'mode'                     => 'utf-8',
            'format'                   => $pageSize,
            'allow_charset_conversion' => true,
            'default_font_size'        => 0,
            'margin_left'              => $pdfMargin['left'],
            'margin_right'             => $pdfMargin['right'],
            'margin_top'               => ($this->helperData->getPositionOfPageNum($storeId) === 'top' && (int) $pdfMargin['top'] < 10) ? 10 : $pdfMargin['top'],
            'margin_bottom'            => $pdfMargin['bottom'],
            'margin_header'            => 0,
            'margin_footer'            => 0,
            'fontDir'                  => array_merge($fontDirs, [$this->getFontDirectory()]),
            'fontdata'                 => $fontData + $this->getCustomFontData(),
            'default_font'             => 'roboto',
            'orientation'              => 'P',
            'tempDir'                  => BP . '/var/tmp',
            'autoScriptToLang'         => true,
            'baseScript'               => 1,
            'autoVietnamese'           => true,
            'autoArabic'               => true,
            'autoLangToFont'           => true,
            'watermark_font'           => 'dejavusanscondensed',
            'debug'                    => false,
        ];
    }

    /**
     * Get custom font data configuration
     *
     * @return array
     */
    private function getCustomFontData()
    {
        return [
            'roboto'                => [
                'R'  => 'roboto/Roboto-Regular.ttf',
                'B'  => 'roboto/Roboto-Bold.ttf',
                'I'  => 'roboto/Roboto-Italic.ttf',
                'BI' => 'roboto/Roboto-BoldCondensedItalic.ttf',
            ],
            'lato'                  => [
                'R'  => 'lato/Lato-Regular.ttf',
                'B'  => 'lato/Lato-Bold.ttf',
                'I'  => 'lato/Lato-Italic.ttf',
                'BI' => 'lato/Lato-BoldItalic.ttf',
            ],
            'roboto_condensed'      => [
                'R'  => 'roboto_condensed/RobotoCondensed-Regular.ttf',
                'B'  => 'roboto_condensed/RobotoCondensed-Bold.ttf',
                'I'  => 'roboto_condensed/RobotoCondensed-Italic.ttf',
                'BI' => 'roboto_condensed/RobotoCondensed-BoldItalic.ttf',
                'L'  => 'roboto_condensed/RobotoCondensed-Light.ttf',
                'LI' => 'roboto_condensed/RobotoCondensed-LightItalic.ttf',
            ],
            'dejavusanscondensed'   => [
                'R'          => 'dejavu/DejaVuSansCondensed.ttf',
                'B'          => 'dejavu/DejaVuSansCondensed-Bold.ttf',
                'I'          => 'dejavu/DejaVuSansCondensed-Oblique.ttf',
                'BI'         => 'dejavu/DejaVuSansCondensed-BoldOblique.ttf',
                'useOTL'     => 255,
                'useKashida' => 75,
            ],
            'opens_san'             => [
                'R'  => 'opens_san/OpenSans-Regular.ttf',
                'B'  => 'opens_san/OpenSans-Bold.ttf',
                'I'  => 'opens_san/OpenSans-Italic.ttf',
                'BI' => 'opens_san/OpenSans-BoldItalic.ttf',
            ],
            'oswald'                => [
                'R' => 'oswald/Oswald-Regular.ttf',
                'B' => 'oswald/Oswald-Bold.ttf',
                'L' => 'oswald/Oswald-Light.ttf',
            ],
            'montserrat'            => [
                'R'  => 'montserrat/Montserrat-Regular.ttf',
                'B'  => 'montserrat/Montserrat-Bold.ttf',
                'I'  => 'montserrat/Montserrat-Italic.ttf',
                'BI' => 'montserrat/Montserrat-BoldItalic.ttf',
            ],
            'fontawesome'           => [
                'R' => 'fontawesome/fontawesome-webfont.ttf',
            ],
            'xbriyaz'               => [
                'R'          => 'XB Riyaz.ttf',
                'B'          => 'XB RiyazBd.ttf',
                'I'          => 'XB RiyazIt.ttf',
                'BI'         => 'XB RiyazBdIt.ttf',
                'useOTL'     => 0xFF,
                'useKashida' => 75,
            ],
            'lateef'                => [
                'R'          => 'LateefRegOT.ttf',
                'useOTL'     => 0xFF,
                'useKashida' => 75,
            ],
            'kfgqpcuthmantahanaskh' => [
                'R'          => 'Uthman.otf',
                'useOTL'     => 0xFF,
                'useKashida' => 75,
            ],
            'scheherazade'          => [
                'R'          => 'scheherazade/ScheherazadeNew-Regular.ttf',
                'B'          => 'scheherazade/ScheherazadeNew-Bold.ttf',
                'useOTL'     => 0xFF,
                'useKashida' => 75,
            ],
        ];
    }

    /**
     * Add custom template vars
     *
     * @param array $templateVars
     * @param int $storeId
     *
     * @return mixed
     */
    public function addCustomTemplateVars($templateVars, $storeId)
    {
        if (!empty($this->getLogoUrl($storeId))) {
            $templateVars['logo_url'] = $this->getLogoUrl($storeId);
        }

        $templateVars['logo_white_url']      = $this->getLogoUrl($storeId, 'white');
        $templateVars['businessInformation'] = $this->getBusinessInformation($storeId);
        $templateVars['pdfInvoiceDesign']    = new DataObject($this->helperData->getPdfDesign('', $storeId));
        $templateVars['pdfInvoiceCustom']    = $this->customFunction;

        return $templateVars;
    }

    /**
     * Return payment info block as html
     *
     * @param Order $order
     * @param int $storeId
     *
     * @return string
     * @throws Exception
     */
    protected function getPaymentHtml(Order $order, $storeId)
    {
        $payment = $order->getPayment();
        if (!$payment) {
            return '';
        }

        try {
            return $this->paymentHelper->getInfoBlockHtml(
                $payment,
                $storeId
            );
        } catch (\Exception $e) {
            return $payment->getMethod() ?: '';
        }
    }

    /**
     * Get format shipping address
     *
     * @param Order $order
     *
     * @return string|null
     */
    protected function getFormattedShippingAddress($order)
    {
        return $order->getIsVirtual()
            ? null
            : $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }

    /**
     * Get format Billing address
     *
     * @param Order $order
     *
     * @return string|null
     */
    protected function getFormattedBillingAddress($order)
    {
        return $this->addressRenderer->format($order->getBillingAddress(), 'html');
    }

    /**
     * Get data preview
     *
     * @param string $type
     * @param null $id
     *
     * @return CreditmemoInterface|InvoiceInterface|OrderInterface|ShipmentInterface
     */
    public function getDataOrder($type = Type::INVOICE, $id = null)
    {
        switch ($type) {
            case Type::CREDIT_MEMO:
                if (empty($id)) {
                    $id = $this->creditmemo->getCollection()->getFirstItem()->getId();
                }

                $this->setComment($this->creditmemo->load($id));
                $model = $this->creditmemoRepository->get($id);

                break;
            case Type::ORDER:
                if (empty($id)) {
                    $id = $this->order->getCollection()->getFirstItem()->getId();
                }
                $model = $this->orderRepository->get($id);
                break;
            case Type::SHIPMENT:
                if (empty($id)) {
                    $id = $this->shipment->getCollection()->getFirstItem()->getId();
                }
                $this->setComment($this->shipment->load($id));
                $model = $this->shipmentRepository->get($id);
                break;
            default:
                if (empty($id)) {
                    $id = $this->invoice->getCollection()->getFirstItem()->getId();
                }

                $this->setComment($this->invoice->load($id));
                $model = $this->invoiceRepository->get($id);
        }

        if (strpos($this->helperData->getFileName($type, $model->getStoreId()), '%increment_id') !== false) {
            $this->fileName = str_replace(
                "%increment_id",
                $model->getIncrementId(),
                $this->helperData->getFileName($type, $model->getStoreId())
            );
        } else {
            if (!is_null($this->helperData->getFileName($type, $model->getStoreId()))) {
                $this->fileName = $this->helperData->getFileName($type, $model->getStoreId());
            } else {
                $this->fileName = $type . $model->getIncrementId();
            }
        }

        return $model;
    }

    /**
     * Set comment
     *
     * @param Invoice|Shipment|Creditmemo $model
     */
    public function setComment($model)
    {
        $this->comment = $model->getCustomerNoteNotify() ? $model->getCustomerNote() : '';
    }

    /**
     * Get Comment
     *
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param null $storeId
     * @param string $type
     *
     * @return string
     */
    public function getLogoUrl($storeId = null, $type = 'black')
    {
        $logoUrl = '';
        $logoPdf = $type === 'white'
            ? $this->helperData->getWhiteLogoPDF($storeId)
            : $this->helperData->getLogoPDF($storeId);
        if (!empty($logoPdf)) {
            $logoUrl = $this->_urlBuilder->getBaseUrl(['_type' => 'media']) . 'mageplaza/pdfinvoice/' . $logoPdf;
        }

        return $logoUrl;
    }

    /**
     *
     * @param string $countryId
     *
     * @return string
     */
    protected function getCachedCountryName($countryId)
    {
        if (!isset($this->countryCache[$countryId])) {
            try {
                $this->countryCache[$countryId] = $this->_countryFactory->create()
                    ->loadByCode($countryId)->getName();
            } catch (\Exception $e) {
                $this->countryCache[$countryId] = $countryId;
            }
        }

        return $this->countryCache[$countryId];
    }

    /**
     *
     * @param int $regionId
     *
     * @return string
     */
    protected function getCachedRegionName($regionId)
    {
        if (!isset($this->regionCache[$regionId])) {
            try {
                $region                       = $this->regionFactory->create()->load($regionId);
                $this->regionCache[$regionId] = $region && $region->getId() ? $region->getName() : '';
            } catch (\Exception $e) {
                $this->regionCache[$regionId] = '';
            }
        }

        return $this->regionCache[$regionId];
    }

    /**
     * Get business information
     *
     * @param int $storeId
     *
     * @return DataObject|mixed
     */
    public function getBusinessInformation($storeId)
    {
        $cacheKey = 'business_info_' . $storeId;
        if (isset($this->businessInfoCache[$cacheKey])) {
            return $this->businessInfoCache[$cacheKey];
        }

        $data = [];
        if (is_array($this->helperData->getBusinessInformationConfig('', $storeId))) {
            $data = $this->helperData->getBusinessInformationConfig('', $storeId);
        }

        if ($this->helperData->isEnableEInvoice($storeId) && !empty($data['street_line1'])) {
            $addressParts = [];

            if (!empty($data['street_line1'])) {
                $addressParts[] = $data['street_line1'];
            }

            if (!empty($data['street_line2'])) {
                $addressParts[] = $data['street_line2'];
            }

            if (!empty($data['city'])) {
                $addressParts[] = $data['city'];
            }

            if (!empty($data['postcode'])) {
                $addressParts[] = $data['postcode'];
            }

            if (!empty($data['region_id'])) {
                $regionName = $this->getCachedRegionName($data['region_id']);
                if ($regionName) {
                    $addressParts[] = $regionName;
                } else {
                    $addressParts[] = !empty($data['region']) ? $data['region'] : $data['region_id'];
                }
            } elseif (!empty($data['region'])) {
                $addressParts[] = $data['region'];
            }

            if (!empty($data['country_id'])) {
                $countryName = $this->getCachedCountryName($data['country_id']);
                if ($countryName) {
                    $addressParts[] = $countryName;
                }
            }

            $data['address'] = implode(', ', array_filter($addressParts));

            $data['phone']   = $data['contact_phone'] ?? $data['phone'] ?? '';
            $data['contact'] = $data['contact_email'] ?? $data['contact'] ?? '';
        }

        if ($data['logo_width'] === null) {
            $data['logo_width'] = 180;
        }

        if ($data['logo_height'] === null) {
            $data['logo_height'] = 30;
        }

        $result = new DataObject($data);

        $this->businessInfoCache[$cacheKey] = $result;

        return $result;
    }

    /**
     * @param string $type
     * @param null $id
     *
     * @return mixed
     * @throws Exception
     * @throws LocalizedException
     */
    public function getDataProcess($type, $id = null)
    {
        $dataOrder = $this->getDataOrder($type, $id);
        $typeTitle = $type;
        if ($type === 'order') {
            /** @var Order $order */
            $order = $dataOrder;
            $data  = ['order' => $dataOrder];
        } else {
            /** @var Order $order */
            $orderId = $dataOrder->getOrderId();
            $order   = $this->orderRepository->get($orderId);
            $data    = [
                'order'       => $order,
                'comment'     => $this->getComment(),
                $type         => $dataOrder,
                $type . '_id' => $dataOrder->getEntityId(),
            ];
        }
        $billingAddress                      = $order->getBillingAddress();
        $shippingAddress                     = $order->getShippingAddress();
        $storeId                             = $this->checkStoreId($order->getStore()->getId());
        $imgHeaderUrl                        = $this->getHeaderImgUrl();
        $data['order_id']                    = $order->getId();
        $data['payment_html']                = $this->getPaymentHtml($order, $storeId);
        $payment                             = $order->getPayment();
        $data['payment_title']               = $payment ? $payment->getMethod() : '';
        $data['store']                       = $order->getStore();
        $data['formattedShippingAddress']    = $this->getFormattedShippingAddress($order);
        $data['shippingAddress']             = $shippingAddress;
        $data['shippingAddress_getStreet']   = $this->getStreet($shippingAddress);
        $data['shippingAddress_getFullName'] = $this->getFullName($shippingAddress);
        $data['countryShipping']             = $this->getCountryById($shippingAddress);
        $data['formattedBillingAddress']     = $this->getFormattedBillingAddress($order);
        $data['billingAddress']              = $order->getBillingAddress();
        $data['billingAddress_getStreet']    = $this->getStreet($billingAddress);
        $data['billingAddress_getFullName']  = $this->getFullName($billingAddress);
        $data['countryBilling']              = $this->getCountryById($billingAddress);
        $data[$typeTitle . 'Note']           = $this->helperData->getPdfNote($type, $storeId);
        $data['commentText']                 = $this->getCommentText($order);
        $data['commentLabel']                = 'Notes for this Order';
        $data['header4']                     = $imgHeaderUrl->getData('header4');
        $data['header5']                     = $imgHeaderUrl->getData('header5');
        $data['customer_name']               = $order->getCustomerName();
        $data['order_date']                  = $this->customFunction->formatDate($order->getData('created_at'));
        if ($type !== 'order') {
            $data[$type . '_date'] = $this->customFunction->formatDate($data[$type]->getData('created_at'));
        }
        if ($type === 'shipment') {
            $data['shipment_amount'] = $order->formatPriceTxt($order->getShippingAmount());
        } else {
            $data[$type . '_amount'] = $order->formatPriceTxt($data[$type]->getGrandTotal());
        }
        $data['shipping_amount'] = $order->formatPriceTxt($order->getShippingAmount());
        $data['subtotal']        = $order->formatPriceTxt($order->getSubtotal());
        $data['grand_total']     = $order->formatPriceTxt($order->getGrandTotal());
        $data['total_due']       = $order->formatPriceTxt($order->getTotalDue());
        $data['tax_amount']      = $order->formatPriceTxt($order->getTaxAmount());
        $data['isNotVirtual']    = $order->getIsNotVirtual();

        return $this->addCustomTemplateVars($data, $storeId);
    }

    /**
     * @param Order $order
     *
     * @return string
     */
    public function getCommentText($order)
    {
        $commentText = '';
        $comments    = [];
        foreach ($order->getStatusHistoryCollection() as $comment) {
            if ($comment->getIsVisibleOnFront()) {
                $comments[] = $comment->getComment();
            }
        }
        for ($i = 0; $i < count($comments); $i++) {
            if ($i === count($comments) - 1) {
                $commentText .= $comments[$i];
            } else {
                $commentText .= $comments[$i] . " | ";
            }
        }

        return $commentText;
    }

    /**
     * @param Address $address
     *
     * @return bool|string
     */
    public function getFullName($address)
    {
        if ($address) {
            if ($address->getMiddlename()) {
                return $address->getFirstname() . ' ' .
                    $address->getMiddlename() . ' ' .
                    $address->getLastName();
            }

            return $address->getFirstname() . ' ' .
                $address->getLastName();
        }

        return '';
    }

    /**
     * @param Order $order
     * @param string $type
     *
     * @return object
     */
    public function getCollection($order, $type)
    {
        switch ($type) {
            case Type::CREDIT_MEMO:
                $collection = $order->getCreditmemosCollection();
                break;
            case Type::SHIPMENT:
                $collection = $order->getShipmentsCollection();
                break;
            default:
                $collection = $order->getInvoiceCollection();
        }

        return $collection;
    }

    /**
     * @param string $type
     * @param int $id
     *
     * @return string
     * @throws Exception
     * @throws LocalizedException
     */
    public function printPdf($type, $id)
    {
        $data    = $this->getDataProcess($type, $id);
        $store   = $data['store'];
        $storeId = $this->checkStoreId($store->getId());
        switch ($type) {
            case Type::CREDIT_MEMO:
                $templateId = $this->helperData->getPdfTemplate(Type::CREDIT_MEMO, $storeId);
                break;
            case Type::ORDER:
                $templateId = $this->helperData->getPdfTemplate(Type::ORDER, $storeId);
                break;
            case Type::SHIPMENT:
                $templateId = $this->helperData->getPdfTemplate(Type::SHIPMENT, $storeId);
                break;
            default:
                $templateId = $this->helperData->getPdfTemplate(Type::INVOICE, $storeId);
        }
        $templateHtml = $this->getTemplateHtml($templateId, $type);

        return $this->getPDFContent($templateHtml, $data, 'D', $storeId);
    }

    /**
     * Get font directory
     *
     * @return string
     */
    public function getFontDirectory()
    {
        return $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'Mageplaza_PdfInvoice') . '/Fonts';
    }

    /**
     * Get base template path
     *
     * @return string
     */
    public function getBaseTemplatePath()
    {
        // Get directory of Data.php
        $currentDir = __DIR__;

        // Get root directory(path of magento's project folder)
        $rootPath = $this->directoryList->getRoot();

        $currentDirArr = explode('\\', $currentDir);
        if (count($currentDirArr) === 1) {
            $currentDirArr = explode('/', $currentDir);
        }

        $rootPathArr = explode('/', $rootPath);
        if (count($rootPathArr) === 1) {
            $rootPathArr = explode('\\', $rootPath);
        }

        $basePath           = '';
        $rootPathArrCount   = count($rootPathArr);
        $currentDirArrCount = count($currentDirArr);
        for ($i = $rootPathArrCount; $i < $currentDirArrCount - 1; $i++) {
            $basePath .= $currentDirArr[$i] . '/';
        }

        return $basePath . 'view/base/templates/default/';
    }

    /**
     * @param string $type
     * @param Order $object
     * @param null $storeId
     *
     * @return bool
     */
    public function isAllowCustomerGroup($type, $object, $storeId = null)
    {
        if (!$this->helperData->applyForGroups($type, $storeId)) {
            return true;
        }

        $customerId = $object->getCustomerId();

        if ($customerId) {
            $customerGroup = $this->customerFactory->create()->load($customerId)->getGroupId();
        } else {
            $customerGroup = '0';
        }

        return in_array($customerGroup, explode(',', $this->helperData->allowCustomerGroups($type, $storeId)), true);
    }

    /**
     * @param OrderAddressInterface|null $address
     *
     * @return string
     */
    public function getStreet($address)
    {
        if ($address) {
            $street = $address->getStreet();
            if (is_array($street)) {
                $streetString = '';
                for ($i = 0; $i < count($street); $i++) {
                    $streetString .= ' ' . trim($street[$i]);
                }

                return $streetString;
            } else {
                if (is_string($street)) {
                    return $street;
                }
            }
        }

        return '';
    }

    /**
     * @param OrderAddressInterface|null $address
     *
     * @return string
     */
    public function getCountryById(?OrderAddressInterface $address)
    {
        if ($address) {
            $countryId = $address->getCountryId();
            if ($countryId) {
                return $this->getCachedCountryName($countryId);
            }
        }

        return '';
    }

    /**
     * @return DataObject
     */
    public function getHeaderImgUrl()
    {
        return new DataObject([
            'header4' => $this->_assetRepo->getUrl("Mageplaza_PdfInvoice::images/header/header-template4.png"),
            'header5' => $this->_assetRepo->getUrl("Mageplaza_PdfInvoice::images/header/header-template5.png"),
        ]);
    }

    /**
     * @param $orderId
     * @param $type
     *
     * @return bool
     */
    public function validateCondition($type, $orderId)
    {
        return $this->helperData->validateConditionRule($type, $orderId);
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getCurrentStore()
    {
        return $this->storeManager->getStore();
    }

    /**
     * Get bundle item details for ZUGFeRD XML
     *
     * @param $item
     * @param $invoice
     *
     * @return string|null
     */
    private function getBundleItemDetails($item, $invoice)
    {
        $details = [];

        // Get child items of the bundle
        foreach ($invoice->getAllItems() as $childItem) {
            if ($childItem->getParentId() == $item->getId()) {
                $details[] = sprintf(
                    '%s (Qty: %s)',
                    $childItem->getName(),
                    $childItem->getQty()
                );
            }
        }

        return !empty($details) ? implode(', ', $details) : null;
    }

    /**
     * Check if item should be included in ZUGFeRD XML based on product type
     *
     * @param $item
     * @param string $productType
     *
     * @return bool
     */
    private function shouldIncludeItemInZugferd($item, $productType)
    {
        switch ($productType) {
            case 'bundle':
                // Only include parent bundle items, exclude children
                return !$item->getParentId();

            case 'virtual':
            case 'downloadable':
                // Include virtual/downloadable products even with zero physical quantity
                return $item->getRowTotal() > 0;

            case 'configurable':
                // For configurable products, exclude parent items and include only child items
                return $item->getParentId() || !$this->hasConfigurableChildren($item);

            default:
                // For simple products and others
                return $item->getQty() > 0 && $item->getRowTotal() > 0;
        }
    }

    /**
     * Check if a configurable item has children
     *
     * @param $item
     *
     * @return bool
     */
    private function hasConfigurableChildren($item)
    {
        foreach ($item->getOrder()->getAllItems() as $orderItem) {
            if ($orderItem->getParentId() == $item->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get appropriate product description for ZUGFeRD XML
     *
     * @param $item
     * @param string $productType
     * @param $invoice
     *
     * @return string|null
     */
    private function getZugferdProductDescription($item, $productType, $invoice)
    {
        switch ($productType) {
            case 'bundle':
                $description   = __('Bundle Product');
                $bundleDetails = $this->getBundleItemDetails($item, $invoice);
                if ($bundleDetails) {
                    $description .= ' - Contains: ' . $bundleDetails;
                }

                return $description;
            case 'virtual':
                return __('Virtual Product - Digital Service');
            case 'downloadable':
                return __('Downloadable Product - Digital Download');
            case 'configurable':
                return __('Configurable Product');
            default:
                return null;
        }
    }

    /**
     * Get product quantity for ZUGFeRD XML based on product type
     *
     * @param $item
     * @param string $productType
     *
     * @return float
     */
    private function getZugferdProductQuantity($item, $productType)
    {
        $quantity = $item->getQty();

        // Handle special cases for virtual/downloadable products
        if (in_array($productType, ['virtual', 'downloadable'])) {
            return max(1, $quantity);
        }

        return $quantity;
    }

    /**
     * Check if invoice contains only virtual/downloadable products
     *
     * @param $invoice
     *
     * @return bool
     */
    private function isInvoiceOnlyVirtualDownloadable($invoice)
    {
        $hasPhysicalProducts = false;

        foreach ($invoice->getAllItems() as $item) {
            if (!$item->getRowTotal()) {
                continue;
            }

            $orderItem   = $item->getOrderItem();
            $productType = $orderItem->getProductType();

            if (!$this->shouldIncludeItemInZugferd($item, $productType)) {
                continue;
            }

            if (!in_array($productType, ['virtual', 'downloadable'])) {
                $hasPhysicalProducts = true;
                break;
            }
        }

        return !$hasPhysicalProducts;
    }
}
