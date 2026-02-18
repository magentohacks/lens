<?php
namespace Lens\Manager\Controller\Manager;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Lens\Manager\Helper\Data as Helper;
use Magento\Checkout\Model\CartFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;

class Reorder extends Action
{
    /**
     *  Constructor function
     */
    public function __construct(
        Helper $helper,
        CartFactory $cart,
        Context $context,
        Validator $validate,
        ProductFactory $productFactory,
        ProductRepository $productRepo,
        OrderRepositoryInterface $orderRepo
    ) {
        $this->cart = $cart;
        $this->helper = $helper;
        $this->validator = $validate;
        $this->orderRepo = $orderRepo;
        $this->product = $productFactory;
        $this->productRepository = $productRepo;
        parent::__construct($context);
    }

    /**
     * Execute function for class CheckAvailibility
     */
    public function execute()
    {
        if ($this->validator->validate($this->getRequest())) {
            $data = [
                'message' => __("Something Went Wrong"),
                'product_sku' => '',
                'success' => false
            ];
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            try {
                $params = $this->getRequest()->getParams();
                $orderId = $params['order_id'];
                $order = $this->orderRepo->get($orderId);
                $items = $order->getAllVisibleItems();
                foreach ($items as $item) {
                    $finalOptionArray = [];
                    $sku = $item->getName();
                    $data['product_sku'] = $sku;
                    $data['order_id'] = $orderId;
                    $product = $this->productRepository->get($sku);
                    $productId = $product->getId();
                    if (empty($item->getParentItemId()) &&  $item->getProductType() == 'simple') {
                        $qty = $item->getQtyOrdered();
                        $cart = $this->addProduct($productId, $finalOptionArray, $qty);
                        continue;
                    }
                    $options = $item->getProductOptions();
                    $qty = $options['info_buyRequest']['qty'];
                    $optionData = $options['attributes_info'];
                    $requiredOptions = [];
                    foreach ($optionData as $productOption) {
                        $requiredOptions[$productOption['label']] = $productOption['value'];
                    }
                    $productOptions = $this->getProductOptions($product, $requiredOptions);
                    foreach ($requiredOptions as $optionLabel => $requiredOption) {
                        if (isset($productOptions[$optionLabel])) {
                            $optionId = array_keys($productOptions[$optionLabel])[0];
                            if (
                                isset($productOptions[$optionLabel][$optionId])
                                && isset($productOptions[$optionLabel][$optionId][$requiredOption])
                            ){
                                $optionValue = $productOptions[$optionLabel][$optionId][$requiredOption];
                                $finalOptionArray[$productId][$optionId] = $optionValue;
                            }
                            $optionValue = $productOptions[$optionLabel][$optionId][$requiredOption];
                            $finalOptionArray[$productId][$optionId] = $optionValue;
                        }
                    }
                    $cart = $this->addProduct($productId, $finalOptionArray, $qty);
                }
                $cart->save();
                $data = [
                    'message' => __("Product Added Successfulyy"),
                    'success' => true
                ];
            } catch (\Exception $e) {
                $data['exception_message'] = $e->getMessage();
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/missing_products.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info(print_r($data, true));
                $resultJson->setData($data);
                return $resultJson;
            }
            $resultJson->setData($data);
            return $resultJson;
        }
    }

    /**
     * function to get all product optiond
     *
     * @param \Magento\Catalog\Model\Product
     *
     * @return array
     */
    public function getProductOptions($product)
    {
        $optionsData = [];
        $options = $product->getOptions();
        foreach ($options as $eachOption) {
            $optionId = $eachOption->getOptionId();
            if ($eachOption->getType() == 'drop_down') {
                $valuesData = [];
                $values = $eachOption->getValues();
                if (is_array($values) && !empty($values)) {
                    foreach ($values as $eachValue) {
                        $valuesData[$optionId][$eachValue->getTitle()] = $eachValue->getOptionTypeId();
                    }
                }
                $optionsData[$eachOption->getTitle()] = $valuesData;
            }
        }
        return $optionsData;
    }

    /**
     * function to add product to cart
     *
     * @param int $productId
     * @param array $optionData
     */
    public function addProduct($productId, $optionData, $qty)
    {
        $cart = $this->cart->create();
        $params = [];
        $params['qty'] = $qty;
        $params['product'] = $productId;
        if (!empty($optionData)) {
            $params['options'] = $optionData[$productId];
        }
        $product = $this->product->create()->load($productId);
        $cart->addProduct($product, $params);
        return $cart;
    }
}
