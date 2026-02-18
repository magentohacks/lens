<?php
namespace Lens\Manager\Controller\Manager;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Test extends Action
{

   public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $productId = 12;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
        $productTypeInstance = $objectManager->get('Magento\ConfigurableProduct\Model\Product\Type\Configurable');
        $productAttributeOptions = $productTypeInstance->getConfigurableAttributesAsArray($product);
        $myArray = [];
        $labelArray = [];
        foreach ($productAttributeOptions as $values) {
            $labelArray[] = $values['label'];
            foreach ($values['values'] as $value) {
                $myArray[$values['label']][] = $value['label']; 
            }
        }
        echo "<pre>";
        $sku = $product->getSku();
        $labelArray['sku'] = "SKU";
        $f = $this->getCombinations($myArray);
        $file = fopen($sku.".csv","w");
        fputcsv($file, $labelArray);
        foreach ($f as $line) {
            $line['sku'] = $sku;
            fputcsv($file, $line);
        }
        fclose($file);
        $finalArray = [];
    }


    public function printCsvFile()
    {
        foreach ($list as $line) {
            fputcsv($file, $line);
        }

        
    }

    public function getCombinations($arrays) {
        $result = array(array());
        foreach ($arrays as $property => $property_values) {
            $tmp = array();
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, array($property => $property_value));
                }
            }
            $result = $tmp;
        }
        return $result;
    }

}