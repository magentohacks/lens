<?php

namespace Lens\Manager\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\CartFactory;
use Magento\Checkout\Model\Session;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product\OptionFactory;

class ProductAddToCartBefore implements ObserverInterface
{
    /**
     * Http Request
     *
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * Cart $cart
     *
     * @car Cart
     */
    protected $cart;

    /**
     * eyeTypeOptionId $eyeTypeOptionId
     */
    protected $eyeTypeOptionId;

    /**
     * Session $session
     */
    private $session;

    /**
     * option $option
     */
    private $option;

    /**
     * product $product
     */
    private $product;

    /**
     * @param \Magento\Framework\App\Request\Http $request
     * @param OptionFactory $option
     * @param Cart $cart
     * @param Session $session
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        OptionFactory $option,
        ProductFactory $product,
        Session $session,
        CartFactory $cart,
        array $data = []
    ) {
        $this->eyeTypeOptionId = '';
        $this->cart = $cart;
        $this->option = $option;
        $this->product = $product;
        $this->session = $session;
        $this->request = $request;
    }

    /**
     *
     *  @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $params = $this->request->getParams();
        $session = $this->session->getQuote();
        if (isset($params['eye_side_id'])) {
            $this->eyeTypeOptionId = $params['eye_side_id'];
        } else {
            $this->eyeTypeOptionId = '';
        }

        if (isset($params['product'])) {
            $productId = $params['product'];
        }
        if (
            (isset($params['left_options'])
            && !empty($params['left_options']))
            || (isset($params['right_options'])
            && !empty($params['right_options']))        
        ) {
            if (isset($params['left_options']) && !empty($params['left_options'])) {
                $params['left_options'][$this->eyeTypeOptionId] = 'Left';
                $cart = $this->addProduct(
                    $productId,
                    $params['left_options'],
                    $params['left_qty'],
                    $params['am_rec_subscription_plan_id'] ?? null,
                    $params['subscribe'] ?? null
                );
                $cart->save();
            }
            if (isset($params['right_options']) && !empty($params['right_options'])) {
                $params['right_options'][$this->eyeTypeOptionId] = 'Right';
                $cart = $this->addProduct(
                    $productId,
                    $params['right_options'],
                    $params['right_qty'],
                    $params['am_rec_subscription_plan_id'] ?? null,
                    $params['subscribe'] ?? null
                );
                $cart->save();
            }
            $cart->save();
            $observer->getRequest()->setParam('product', false);
            $observer->getRequest()->setParam('return_url', false);
            $session->setTriggerRecollect(1);
            $session->collectTotals();
            $session->save();
        }
    }

    /**
     * function to add product to cart
     *
     * @param int $productId
     * @param array $optionData
     */
    public function addProduct($productId, $optionData, $qty, $subscriptionPlan = null, $isSubscribe = null)
    {
        $cart = $this->cart->create();
        $params = [];
        $params['qty'] = $qty;
        $params['product'] = $productId;
        foreach ($optionData as $optionId => $value) {
            $options[$optionId] = $value;
        }
        $params['options'] = $options;

         if ($subscriptionPlan && $isSubscribe) {
            $params['am_rec_subscription_plan_id'] = $subscriptionPlan;
            $params['subscribe'] = $isSubscribe;
        }

        $product = $this->product->create()->load($productId);
        $cart->addProduct($product, $params);
        return $cart;
    }
}