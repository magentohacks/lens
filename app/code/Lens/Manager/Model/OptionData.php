<?php
namespace Lens\Manager\Model;

class OptionData
{

    public $resource;

    public $odOption;

    public $storeManager;

    public $odValue;

    public $dependantValue;

    public $dependantOption;
    
    public function __construct(
        \Pektsekye\OptionDependent\Model\Option $odOption,
        \Pektsekye\OptionDependent\Model\Value $odValue,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resource = $resource;
        $this->dependantOption = $odOption;
        $this->storeManager = $storeManager;
        $this->dependantValue = $odValue;
    }

    /**
     * Function to get Product's Options Data
     * 
     * @return array
     */
    public function getOptionData($productId, $options)
    {
        $finalArray = [];
        $optionRowMapping = $this->getValuesByProductId($productId);
        
        foreach ($options as $eachOption) {
            if ($eachOption->getType() == 'drop_down') {
                $valueData = [];
                $values = $eachOption->getValues();
                foreach ($values as $value) {
                    $children = $this->dependantValue->getCollection()
                    ->addFieldToFilter('option_type_id', ['eq' => $value->getId()])
                    ->getFirstItem()
                    ->getChildren();
                    if ($children != "") {
                        foreach (explode(",", $children) as $child) {
                            $valueData[$value->getId()][$optionRowMapping[$child]['option_id']][$optionRowMapping[$child]['value_id']] = $optionRowMapping[$child]['title'];
                        }
                    }
                }
                $finalArray[$eachOption->getId()] = $valueData;
            } else {
                $finalArray['eyeside_optionid'] = $eachOption->getId();
            }
        }
        return $finalArray;
    }

    /**
     * Function to map row Id and option Id
     * 
     * @param int $productId
     */
    public function getValuesByProductId($productId)
    {
        $values = $this->dependantValue->getCollection()
            ->addFieldToFilter('product_id', ['eq' => $productId]);
        $storeId = $this->storeManager->getStore()->getId();
        $cpov = $this->resource->getTableName('catalog_product_option_type_value'); 
        $cpot = $this->resource->getTableName('catalog_product_option_type_title'); 
        $values->getSelect()->joinLeft(
            ['cpov' => $cpov],
            'main_table.option_type_id = cpov.option_type_id'
        );
        $values->getSelect()->joinLeft(
            ['cpot' => $cpot],
            'main_table.option_type_id = cpot.option_type_id',
            ['title']
        )->where('store_id = '.$storeId);
        $optionIdMapChildrenId = [];
        foreach ($values as $eachValue) {
            $optionIdMapChildrenId[$eachValue->getRowId()] = [
                'option_id' => $eachValue->getOptionId(),
                'value_id' => $eachValue->getOptionTypeId(),
                'title' => $eachValue->getTitle()
            ];
        }
        return $optionIdMapChildrenId;
    }
}
?>
