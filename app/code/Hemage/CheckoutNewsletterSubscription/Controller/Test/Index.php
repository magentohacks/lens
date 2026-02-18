<?php
namespace Hemage\CheckoutNewsletterSubscription\Controller\Test;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
class Index extends Action
{

    public function __context(
        Context $context
    ){
        parent::__cnstruct($context);
    }

    public function execute()
    {
        phpinfo();
        $productId = '137350'; // Configurable Product Id
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId); // Load Configurable Product
        die('dsjkbsdf');
        $attributeModel = $objectManager->create('Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute');
        $position = 0;
        die;
        $attributes = array(166, 162, 164, 165,163); // Super Attribute Ids Used To Create Configurable Product
        $i = 137351;
        while ($i < 139508) {
            $arr[] = $i;
            $i++;
        }
        echo "<pre>";print_r($i);die;
        $associatedProductIds = array(2,4,5,6); //Product Ids Of Associated Products
        foreach ($arr as $attributeId) {
            $data = array('attribute_id' => $attributeId, 'product_id' => $productId, 'position' => $position);
            $position++;
            $attributeModel->setData($data)->save();
        }
        $product->setTypeId("configurable"); // Setting Product Type As Configurable
        $product->setAffectConfigurableProductAttributes(4);
        $objectManager->create('Magento\ConfigurableProduct\Model\Product\Type\Configurable')->setUsedProductAttributeIds($attributes, $product);
        $product->setNewVariationsAttributeSetId(4); // Setting Attribute Set Id
        $product->setAssociatedProductIds($arr);// Setting Associated Products
        $product->setCanSaveConfigurableAttributes(true);
        $product->save();
    }
}




?>