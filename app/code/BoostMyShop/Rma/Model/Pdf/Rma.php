<?php

namespace BoostMyShop\Rma\Model\Pdf;


class Rma extends \Magento\Sales\Model\Order\Pdf\AbstractPdf
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    protected $_config;

    /**
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory
     * @param \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\Order\Pdf\Config $pdfConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \BoostMyShop\Rma\Model\Config $config,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_localeResolver = $localeResolver;
        $this->_config = $config;

        parent::__construct(
            $paymentData,
            $string,
            $scopeConfig,
            $filesystem,
            $pdfConfig,
            $pdfTotalFactory,
            $pdfItemsFactory,
            $localeDate,
            $inlineTranslation,
            $addressRenderer,
            $data
        );

    }

    /**
     * Draw header for item table
     *
     * @param \Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(\Zend_Pdf_Page $page)
    {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));

        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 15);
        $this->y -= 10;

        //columns headers
        $lines[0][] = ['text' => __('Qty'), 'feed' => 45, 'align' => 'right'];
        $lines[0][] = ['text' => __('SKU'), 'feed' => 70, 'align' => 'left'];
        $lines[0][] = ['text' => __('Product'), 'feed' => 200, 'align' => 'left'];

        $lineBlock = ['lines' => $lines, 'height' => 5];

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }

    /**
     * Return PDF document
     *
     * @param array|Collection $invoices
     * @return \Zend_Pdf
     */
    public function getPdf($rmas = [])
    {
        $this->_beforeGetPdf();

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($rmas as $rma) {

            if ($rma->getrma_store_id()) {
                $this->_localeResolver->emulate($rma->getrma_store_id());
                $this->_storeManager->setCurrentStore($rma->getrma_store_id());
            }
            $page = $this->newPage();

            /* Add image */
            $this->insertLogo($page, $rma->getStore());

            /* Add document text and number */
            $this->drawRmaInformation($page, $rma);

            $this->drawAddresses($page, $rma);

            $this->_drawHeader($page);

            /* Add body */
            foreach ($rma->getAllItems() as $item) {

                if ($item->getri_qty() == 0)
                    continue;

                /* Draw item */
                $this->_drawProduct($item, $page, $rma);

                $page = end($pdf->pages);
            }

            $this->insertAdditionnal($page, $rma);

            $this->drawInstructions($page, $rma);

            if ($rma->getRmaStoreId()) {
                $this->_localeResolver->revert();
            }
        }
        $this->_afterGetPdf();
        return $pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return \Zend_Pdf_Page
     */
    public function newPage(array $settings = [])
    {
        /* Add new table head */
        $page = $this->_getPdf()->newPage(\Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;
        if (!empty($settings['table_header'])) {
            $this->_drawHeader($page);
        }
        return $page;
    }

    /**
     * @param $item
     * @param $page
     * @param $order
     */
    protected function _drawProduct($item, $page, $rma)
    {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));

        //columns headers
        $lines[0][] = ['text' => $item->getRiQty(), 'feed' => 45, 'align' => 'right'];

        $lines[0][] = ['text' => $item->getri_sku(), 'feed' => 70, 'align' => 'left'];

        $lines[0][] = ['text' => $item->getri_name(), 'feed' => 200, 'align' => 'left'];

        $lineBlock = ['lines' => $lines, 'height' => 5];

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }



    /**
     * Insert billto & shipto blocks
     *
     * @param $page
     * @param $order
     */
    protected function drawAddresses($page, $order)
    {
        /* Add table head */
        $this->_setFontBold($page, 14);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));

        $this->y -= 15;
        $page->drawText(__('Customer :'), 30, $this->y, 'UTF-8');
        $page->drawText(__('Return to :'), 300, $this->y, 'UTF-8');

        $customerAddress = $this->splitTextForLineWeight($this->getCustomerAddress($order), 200, $this->getFontRegular(), 14);
        $returnAddress = explode("\n", $this->getReturnAddress($order));

        $this->_setFontRegular($page, 12);
        $i = 0;
        foreach($customerAddress as $line) {
            $line = str_replace("\r", "", $line);
            if ($line) {
                $page->drawText($line, 60, $this->y - 20 - ($i * 13), 'UTF-8');
                $i++;
            }
        }

        $j = 0;
        foreach($returnAddress as $line) {
            $line = str_replace("\r", "", $line);
            if ($line) {
                $page->drawText($line, 330, $this->y - 20 - ($j * 13), 'UTF-8');
                $j++;
            }
        }

        $maxLines = max(($i), ($j));

        $this->y -= $maxLines * 20;
    }

    public function getCustomerAddress($rma)
    {
        return $rma->getrma_shipping_address();
    }

    public function getReturnAddress($rma)
    {
        return $this->_config->getSetting('general/return_address', $rma->getrma_store_id());
    }

    protected function drawRmaInformation($page, $rma)
    {

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 90);

        $this->_setFontBold($page, 14);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $page->drawText(__('RMA # %1', $rma->getRmaReference()), 30, $this->y - 20, 'UTF-8');

        $this->_setFontRegular($page, 12);
        $additionnalTxt = [];
        if ($rma->getOrder())
            $additionnalTxt[] = __('Original order # : %1', $rma->getOrder()->getincrement_id());
        $additionnalTxt[] = __('Manager : %1', $rma->getManager()->getfirstname().' '.$rma->getManager()->getlastname());
        $additionnalTxt[] = __('Valid until : %1', $rma->getrma_expire_eta());

        $i = 0;
        foreach($additionnalTxt as $txt)
        {
            $page->drawText($txt, 60, $this->y - 40 - ($i * 13), 'UTF-8');
            $i++;
        }

        $this->y -= 100;

    }


    protected function drawInstructions($page, $rma)
    {
        $comments = $rma->getrma_public_comments();
        $comments .= $this->_config->getPdfInstructions();

        if (!$comments)
            return $this;


        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0));
        $page->setLineWidth(0.5);

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 14);
        $page->drawText(__('Instructions :'), 30, $this->y - 20, 'UTF-8');
        $this->_setFontRegular($page, 12);

        $lines = $this->splitTextForLineWeight($comments, 550, $this->getFontRegular(), 14);
        foreach($lines as $line) {

            $line = str_replace("\r", "", $line);
            $line = str_replace("\n", "", $line);

            $page->drawText($line, 60, $this->y - 40, 'UTF-8');
            $this->y -= 20;
            if ($this->y < 100) {
                $page = $this->newPage();
                $this->_setFontRegular($page, 12);
            }
        }


    }

    public function insertAdditionnal($page, $order)
    {

    }

    public function getPdfObject()
    {
        return $this->_getPdf();
    }

    public function setFontBold($page, $size)
    {
        $this->_setFontBold($page, $size);
        return $this;
    }

    public function setFontRegular($page, $size)
    {
        $this->_setFontRegular($page, $size);
        return $this;
    }

    protected function getFontRegular()
    {
        $font = \Zend_Pdf_Font::fontWithPath(
            $this->_rootDirectory->getAbsolutePath('lib/internal/LinLibertineFont/LinLibertine_Re-4.4.1.ttf')
        );
        return $font;
    }

    public function splitTextForLineWeight($txt, $maxWidth, $font, $fontSize)
    {
        $lines = [];
        $words = explode(' ', $txt);
        $currentLine = '';
        foreach($words as $word)
        {
            if ($this->widthForStringUsingFontSize($currentLine.' '.$word, $font, $fontSize) > $maxWidth)
            {
                $lines[] = $currentLine;
                $currentLine = $word;
            }
            else
                $currentLine .= ' '.$word;
        }
        $lines[] = $currentLine;
        return $lines;
    }

}
