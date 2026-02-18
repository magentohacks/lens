<?php
namespace Lens\GoogleCustomerReviews\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;

class Success extends Template
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @param Context $context
     * @param Session $checkoutSession
     * @param OrderFactory $orderFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        OrderFactory $orderFactory,
        array $data = []
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->orderFactory = $orderFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get current order
     *
     * @return Order|null
     */
    public function getOrder()
    {
        if ($this->order === null) {
            $orderId = $this->checkoutSession->getLastOrderId();
            if ($orderId) {
                $this->order = $this->orderFactory->create()->load($orderId);
            }
        }
        return $this->order;
    }

    /**
     * Get merchant ID
     *
     * @return int
     */
    public function getMerchantId()
    {
        return 110307348;
    }

    /**
     * Get order ID
     *
     * @return string
     */
    public function getOrderId()
    {
        $order = $this->getOrder();
        if ($order && $order->getId()) {
            return $order->getIncrementId();
        }
        return '';
    }

    /**
     * Get customer email
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        $order = $this->getOrder();
        if ($order && $order->getId()) {
            return $order->getCustomerEmail();
        }
        return '';
    }

    /**
     * Get delivery country code
     *
     * @return string
     */
    public function getDeliveryCountry()
    {
        $order = $this->getOrder();
        if ($order && $order->getShippingAddress()) {
            return $order->getShippingAddress()->getCountryId();
        }
        if ($order && $order->getBillingAddress()) {
            return $order->getBillingAddress()->getCountryId();
        }
        return '';
    }

    /**
     * Get estimated delivery date
     * Default to 7 days from order date
     *
     * @return string
     */
    public function getEstimatedDeliveryDate()
    {
        $order = $this->getOrder();
        if ($order && $order->getId()) {
            $orderDate = $order->getCreatedAt();
            $deliveryDate = date('Y-m-d', strtotime($orderDate . ' +2 days'));
            return $deliveryDate;
        }
        return '';
    }

    /**
     * Get products with GTIN
     *
     * @return array
     */
    public function getProducts()
    {
        $products = [];
        $order = $this->getOrder();
        
        if ($order && $order->getId()) {
            $items = $order->getAllVisibleItems();
            foreach ($items as $item) {
                $product = $item->getProduct();
                if ($product) {
                    $gtin = $this->getProductGtin($product);
                    if ($gtin) {
                        $products[] = ['gtin' => $gtin];
                    }
                }
            }
        }
        
        return $products;
    }

    /**
     * Get product GTIN (EAN, UPC, ISBN, or JAN)
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string|null
     */
    protected function getProductGtin($product)
    {
        // Try different GTIN attributes
        $gtinAttributes = ['gtin', 'ean', 'upc', 'isbn', 'jan'];
        
        foreach ($gtinAttributes as $attr) {
            $value = $product->getData($attr);
            if ($value) {
                return $value;
            }
        }
        
        return null;
    }

    /**
     * Check if module should be displayed
     *
     * @return bool
     */
    public function shouldDisplay()
    {
        $order = $this->getOrder();
        return $order && $order->getId() && $this->getOrderId() && $this->getCustomerEmail();
    }
}

