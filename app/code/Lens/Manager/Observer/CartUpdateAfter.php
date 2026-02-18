<?php

namespace Lens\Manager\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\CartFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product\OptionFactory;
use Magento\Framework\App\Request\Http;

class CartUpdateAfter implements ObserverInterface
{
    /**
     * Http Request
     *
     * @var Http
     */
    protected $request;

    /**
     * Cart $cart
     *
     * @var Cart
     */
    protected $cart;

    private $eyeTypeOptionId;

    /**
     * @param Http $request
     * @param Cart $cart
     * @param array $data
     */
    public function __construct(
        Http $request,
        CartFactory $cart,
        array $data = []
    ) {
        $this->eyeTypeOptionId = NULL;
        $this->cart = $cart;
        $this->request = $request;
    }

    /**
     *  @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $params = $this->request->getParams();        
        $item = $observer->getItem();
        $productId = $params['product'];
        $itemId = $item->getId();
        $quote = $item->getQuote();
        foreach ($quote->getAllItems() as $eachItem) {
            if ($eachItem->getProductId() == $productId
                && $eachItem->getId() != $itemId) {
                $siblingItemId = $eachItem->getId();
            }
        }
        if (isset($params['eye_side_id'])) {
            $this->eyeTypeOptionId = $params['eye_side_id'];
        } else {
            $this->eyeTypeOptionId = '';
        }
        foreach($item->getOptionsByCode() as $eachOption) {
            $optionData = json_decode($eachOption->getValue(), true);
        }
        if (isset($optionData['left_options']) && !empty($optionData['left_options'])) {
        $params['left_options'][$this->eyeTypeOptionId] = 'Left';
        $cart = $this->updateProduct($itemId, $productId, $params['left_options'], $params['left_qty']);
        if (!empty($siblingItemId) && isset($optionData['right_options']) && !empty($optionData['right_options'])) {
            $params['left_options'][$this->eyeTypeOptionId] = 'Right';
            $cart = $this->updateProduct($siblingItemId, $productId, $params['right_options'], $params['right_qty']);
        }
        }
        if (isset($optionData['right_options']) && !empty($optionData['right_options'])) {
        $params['left_options'][$this->eyeTypeOptionId] = 'Right';
        $cart = $this->updateProduct($itemId, $productId, $params['right_options'], $params['right_qty']);
        if (!empty($siblingItemId) && isset($optionData['left_options']) && !empty($optionData['left_options'])) {
            $params['left_options'][$this->eyeTypeOptionId] = 'Left';
            $cart = $this->updateProduct($siblingItemId, $productId, $params['left_options'], $params['left_qty']);
        }
        }
        $cart->save();
        $quote->setTriggerRecollect(1);
        $quote->collectTotals();
        $quote->save();
    }

    /**
     * function to update product to cart
     *
     * @param int $itemId
     * @param int $productId
     * @param array $optionData
     * @param int $qty
     */
    public function updateProduct($itemId, $productId, $optionData, $qty)
    {
        $cart = $this->cart->create();
        $params = [];
        $params['qty'] = $qty;
        $params['product'] = $productId;
        foreach ($optionData as $optionId => $value) {
            $options[$optionId] = $value;
        }
        $params['options'] = $options;
        $item = $cart->updateItem($itemId, new \Magento\Framework\DataObject($params));
        if (is_string($item)) {
            throw new \Magento\Framework\Exception\LocalizedException(__($item));
        }
        if ($item->getHasError()) {
            throw new \Magento\Framework\Exception\LocalizedException(__($item->getMessage()));
        }
        return $cart;
    }
}
