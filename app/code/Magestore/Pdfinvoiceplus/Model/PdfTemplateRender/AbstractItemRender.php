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

use Magento\Sales\Api\Data\OrderItemInterface;

/**
 * Class AbstractItemRender
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
abstract class AbstractItemRender extends \Magestore\Pdfinvoiceplus\Model\AbstractPdfTemplateRender
{
    /**
     * @var \Magento\Framework\Config\ScopeInterface
     */
    protected $scope;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;

    /**
     * AbstractItemRender constructor.
     * @param \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderManager $pdfTemplateRenderManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     * @param \Magestore\Pdfinvoiceplus\Helper\Data $pdfHelper
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param array $data
     */
    public function __construct(
        \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderManager $pdfTemplateRenderManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Magestore\Pdfinvoiceplus\Helper\Data $pdfHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Config\ScopeInterface $scope,
        array $data = []
    )
    {
        $this->_imageHelper = $imageHelper;
        $this->scope = $scope;
        parent::__construct($pdfTemplateRenderManager, $objectManager, $filesystem, $catalogHelper, $pdfHelper, $data);
    }

    /**
     * Render entity data to a html template
     *
     * @param \Magento\Sales\Model\AbstractModel $entity
     * @param                                    $templateHtml
     *
     * @return mixed
     */
    public function render(\Magento\Sales\Model\AbstractModel $entity, $templateHtml)
    {
        $this->setRenderingEntity($entity);
        $variables = $this->getVariables();

        if (isset($variables['items_product_options'])) {
            $templateHtml = str_replace('{{var items_product_options}}', $variables['items_product_options'], $templateHtml);
            unset($variables['items_product_options']);
        }

        if (isset($variables['items_small_image'])) {
            $templateHtml = str_replace('{{var items_small_image}}', $variables['items_small_image'], $templateHtml);
            unset($variables['items_small_image']);
        }

        return $this->_pdfHelper->mappingVariablesTemplate($templateHtml, $variables);
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
    public function getSku()
    {
        return implode('<br/>', $this->_catalogHelper->splitSku($this->getRenderingEntity()->getSku()));
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->getRenderingEntity()->getName();
    }

    /**
     * @return int
     */
    public function getQty()
    {
        return (int)$this->getRenderingEntity()->getQty();
    }

    /**
     * @return mixed|null
     */
    public function getBundleOptionLabel()
    {
        if (!$this->getOrderItem()->getParentItem()) {
            return null;
        }
        $options = $this->getOrderItem()->getProductOptions();
        if (!isset($options['bundle_selection_attributes'])) {
            return null;
        }

        return unserialize($options['bundle_selection_attributes']);
    }

    /**
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->getRenderingEntity()->getDiscountAmount();
    }

    /**
     * @return string
     */
    public function getTaxAmount()
    {
        return $this->getOrder()->formatPriceTxt($this->getRenderingEntity()->getTaxAmount());
    }

    /**
     * @return string
     */
    public function getTaxPercent()
    {
        $taxpercent = 0;
        foreach ($this->getOrder()->getAllItems() as $item) {
            $taxpercent = $item->getTaxPercent();
        }

        return number_format($taxpercent, 2, ',', '') . '%';
    }

    /**
     * @return array
     */
    public function getItemsPriceData()
    {
        return $this->getItemPricesForDisplay();
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $orderItem
     *
     * @return array
     */
    public function getStandardItemVars()
    {
        return [
            'items_name' => [
                'value' => $this->getRenderingEntity()->getName(),
                'label' => 'Product Name'
            ],
            'bundle_items_option' => [
                'value' => $this->getBundleOptionLabel(),
                'label' => 'Bundle Name'
            ],
            'items_sku' => [
                'value' => $this->getSku(),
                'label' => 'SKU'
            ],
            'items_qty' => [
                'value' => $this->getQty(),
                'label' => 'Qty'
            ],
            'items_tax_amount' => [
                'value' => $this->getTaxAmount(),
                'label' => 'Tax Amount'
            ],
            'items_discount_amount' => [
                'value' => $this->getDiscountAmount(),
                'label' => 'Discount Amount'
            ],
            'items_tax_percent' => [
                'value' => $this->getTaxPercent(),
                'label' => 'Tax Percent'
            ]
        ];
    }

    /**
     * Get the Item prices for display - need to review this part adn move the item system to do
     * @return array
     */
    public function getItemPricesForDisplay()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getOrder();

        $price = [];
        foreach ($this->getRenderingEntity()->getData() as $key => $value) {
            $price['items_' . $key] = array('value' => $value);
            if ($key == 'price_incl_tax') {
                $price['items_price_incl_tax'] = array(
                    'value' => $order->formatPriceTxt($this->getRenderingEntity()->getPriceInclTax())
                );
            }
            if ($key == 'row_total_incl_tax') {
                $price['items_row_total_incl_tax'] = array(
                    'value' => $order->formatPriceTxt($this->getRenderingEntity()->getRowTotalInclTax())
                );
            }
            if ($key == 'price') {
                $price['items_price'] = array(
                    'value' => $order->formatPriceTxt($this->getRenderingEntity()->getPrice())
                );
            }
            if ($key == 'row_total') {
                $price['items_row_total'] = array(
                    'value' => $order->formatPriceTxt($this->getRenderingEntity()->getRowTotal())
                );
            }
        }

        return $price;
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        if ($this->isChildItem() && $this->isConfigurable($this->getParentItem()->getProductId())) {
            return [];
        }

        $imageData = $this->getProductImage($this->getRenderingEntity()->getProductId());

        $itemsPriceData = $this->getItemsPriceData();
        $userAttributeData = $this->getUserAttributeData(
            $this->getRenderingEntity()->getProductId(),
            $this->getOrder()->getStoreId(),
            $this->isChildItem()
        );

        $standardVars = $this->getStandardItemVars();
        $productOptions = $this->getItemOptions();

        $itemData = isset($productOptions)
            ? array_merge($itemsPriceData, $userAttributeData, $standardVars, $productOptions, $imageData)
            : array_merge($itemsPriceData, $userAttributeData, $standardVars, $imageData);

        return $this->_prepareVariablesData($itemData);
    }

    /**
     * @param $productId
     * @return array
     */
    public function getProductImage($productId)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->_objectManager->create('Magento\Catalog\Model\Product');
        $product = $product->load($productId);
        if ($this->scope->getCurrentScope() == \Magento\Framework\App\Area::AREA_FRONTEND) {
            $imagePath = $this->_objectManager->create('Magento\Catalog\Helper\Image')
                ->init($product, 'category_page_list')
                ->constrainOnly(TRUE)
                ->keepAspectRatio(TRUE)
                ->keepFrame(TRUE)
                ->resize(70, 70)
                ->getUrl();
        } else {
            $imagePath = $this->_objectManager->create('Magento\Catalog\Helper\Image')
                ->init($product, 'product_listing_thumbnail')
                ->setImageFile($product->getFile())
                ->getUrl();
        }

        if (isset($imagePath)) {
            $image = [
                'items_small_image' => [
                    'value' => '<img src="' . $imagePath . '" />',
                    'label' => __('Product image'),
                ],
            ];
        } else {
            $image = [
                'items_small_image' => [
                    'value' => '',
                    'label' => '',
                ],
            ];
        }

        return $image;
    }

    /**
     * @param $url
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
     * @return array
     */
    public function isPriceDisplayOptions()
    {
        return $this->isChildCalculated() ? $this->getItemPricesForDisplay() : [];
    }

    /**
     * @return \Magento\Sales\Model\Order\Item
     */
    abstract public function getOrderItem();

    /**
     * @return bool
     */
    public function isChildItem()
    {
        return (bool)$this->getParentItem();
    }

    /**
     * @return OrderItemInterface|null
     */
    public function getParentItem()
    {
        return $this->getOrderItem()->getParentItem();
    }

    /**
     * @return bool
     */
    public function isChildCalculated()
    {
        $item = $this->getOrderItem();
        $options = $item->getParentItem() ? $item->getParentItem()->getProductOptions() : $item->getProductOptions();
        if ($options) {
            if (isset($options['product_calculations']) &&
                $options['product_calculations'] == \Magento\Catalog\Model\Product\Type\AbstractType::CALCULATE_CHILD
            ) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * get all non system product's attributes
     * @return array
     */
    public function getAllNonSystemAttributes()
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $productAttributeCollection */
        $productAttributeCollection = $this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection');
        $productAttributeCollection->addFieldToFilter('is_user_defined', '1');

        $attrs = [];

        foreach ($productAttributeCollection as $attribute) {
            $attrs[$attribute->getData('attribute_code')] = [
                'label' => $attribute->getData('frontend_label'),
            ];
        }

        return $attrs;
    }

    /**
     * @param $productId
     * @param $storeId
     * @param bool $child
     * @return array
     */
    public function getUserAttributeData($productId, $storeId, $child = false)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId);
        $data = [];
        $gettingTheVariablesFromArrayKey = array_keys($this->getAllNonSystemAttributes());
        $gettingTheLabelsFromArrayKey = $this->getAllNonSystemAttributes();

        foreach ($gettingTheVariablesFromArrayKey as $variables) {
            if ($product->offsetExists($variables) && $product->getAttributeText($variables)) {
                $data[$variables] = [
                    'value' => $product->getAttributeText($variables),
                    'label' => $gettingTheLabelsFromArrayKey[$variables]['label'],
                ];
            } else {
                if ($product->getData($variables)) {
                    $data[$variables] = [
                        'value' => $product->getData($variables),
                        'label' => $gettingTheLabelsFromArrayKey[$variables]['label'],
                    ];
                }
            }

            $data['weight'] = [
                'value' => $product->getData('weight'),
                'label' => __('Product weight'),
            ];
            $data['description'] = [
                'value' => $product->getData('description'),
                'label' => 'Product description',
            ];
            $data['short_description'] = [
                'value' => $product->getData('short_description'),
                'label' => __('Product short description'),
            ];

            if (!$child) {
                $data['url_path'] = [
                    'value' => $product->getData('url_path'),
                    'label' => __('Product url path'),
                ];
            }
        }

        return $data;
    }

    /**
     * Retrieve order item's options (product options)
     *
     * @return array
     */
    public function getItemOptions()
    {
        $productOptionsLabeled = [];

        if ($options = $this->getOrderItem()->getProductOptions()) {
            $result = [];
            if ($options) {
                if (isset($options['options'])) {
                    $result = array_merge($result, $options['options']);
                }
                if (isset($options['additional_options'])) {
                    $result = array_merge($result, $options['additional_options']);
                }
                if (isset($options['attributes_info'])) {
                    $result = array_merge($result, $options['attributes_info']);
                }
            }
            /* Will be able to split in three */
            $data = null;

            foreach ($result as $option => $value) {
                if ($value['label'] && $value['value']) {
                    $data .= $value['label'] . ' - ' . $value['value'] . '<br/>';
                }
            }

            $productOptionsLabeled = [
                'items_product_options' => [
                    'value' => $data,
                    'label' => __('Product options'),
                ],
            ];
        }

        return $productOptionsLabeled;
    }
}