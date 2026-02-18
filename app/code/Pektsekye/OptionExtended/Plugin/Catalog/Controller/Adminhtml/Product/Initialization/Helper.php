<?php

namespace Pektsekye\OptionExtended\Plugin\Catalog\Controller\Adminhtml\Product\Initialization;

class Helper
{

    protected $customOptionFactory;         
     
        
    public function __construct(
        \Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory $customOptionFactory = null,    
        \Magento\Framework\App\RequestInterface $request    
    ) {
        $this->customOptionFactory = $customOptionFactory ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory::class);    
        $this->request = $request;  
    } 
 
    
    public function aroundInitializeFromData(\Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $subject, \Closure $proceed, $product, array $productData)
    {    
        if (isset($productData['options'])) {
            $productOptions = $productData['options'];
            unset($productData['options']);
        } else {
            $productOptions = [];
        }       
        
        $proceed($product, $productData);

        if ($productOptions && !$product->getOptionsReadonly()) {
        
            $overwriteOptions = $this->request->getPost('options_use_default');
            if (!empty($overwriteOptions)){
              foreach ($productOptions as $k => $option) {
                $optionId = $option['option_id'];        
                if (isset($overwriteOptions[$optionId]['title']) && $overwriteOptions[$optionId]['title'] == 1){       
                  $productOptions[$k]['is_delete_store_title'] = 1;
                }               
                if (isset($option['values'])) {
                  foreach ($option['values'] as $kk => $value) {
                    $valueId = $value['option_type_id'];                  
                    if (isset($overwriteOptions[$optionId]['values'][$valueId]['title']) && $overwriteOptions[$optionId]['values'][$valueId]['title'] == 1){                  
                      $productOptions[$k]['values'][$kk]['is_delete_store_title'] = 1;
                    }                                      
                  }
                }            
              }
            }        

            $customOptions = [];
            foreach ($productOptions as $customOptionData) {
                if (!empty($customOptionData['is_delete'])) {
                 //   continue; we will delete options later in the file app/code/Pektsekye/OptionExtended/Plugin/Catalog/Model/Product/Option/SaveHandler.php on line 26
                }

                if (empty($customOptionData['option_id'])) {
                    $customOptionData['option_id'] = null;
                }

                if (isset($customOptionData['values'])) {
                    $customOptionData['values'] = array_filter($customOptionData['values'], function ($valueData) {
                        return empty($valueData['is_delete']);
                    });
                }

                $customOption = $this->customOptionFactory->create(['data' => $customOptionData]);
                $customOption->setProductSku($product->getSku());
                $customOptions[] = $customOption;
            }
            $product->setOptions($customOptions);
        }
        
        return $product;        
        
    }


}
