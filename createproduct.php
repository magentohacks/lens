<?php

use Magento\Framework\App\Bootstrap;
require __DIR__ . '/app/bootstrap.php';
$bootstrap = Bootstrap::create(BP, $_SERVER);
ini_set('display_errors', 1);
echo "<pre>";
$obj = $bootstrap->getObjectManager();
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);

$dataBig = [
    [
        'sku' => "Air Optix for Astigmatism",
        'name' => "Air Optix for Astigmatism",
        'attr_set' => "19",
        'weight' => "1.0000",
        'visibility' => "4",
        'tax' => "2",
        'price' => "0",
        'options' => [
            'Power' => [-10.00,-9.50,-9.00,-8.50,-8.00,-7.50,-7.00,-6.50,-6.00,-5.75,-5.50,-5.25,-5.00,-4.75,-4.50,-4.25,-4.00,-3.75,-3.50,-3.25,-3.00,-2.75,-2.50,-2.25,-2.00,-1.75,-1.50,-1.25,-1.00,-0.75,-0.50,-0.25,0.00,+0.25,+0.50,+0.75,+1.00,+1.25,+1.50,+1.75,+2.00,+2.25,+2.50,+2.75,+3.00,+3.25,+3.50,+3.75,+4.00,+4.25,+4.50,+4.75,+5.00,+5.25,+5.50,+5.75,+6.00],
            'Diameter' => ["14.50"],
            "Basecurve" => ['8.7'],
            "Cylinder" => ["-2.25","-1.75","-1.25","-0.75","-1.00"],
            "Axis" =>["10","20","30","40","50","60","70","80","90","100","110","120","130","140","150","160","170","180"]
        ]
    ]
];
foreach ($dataBig as $data) {
    $product = $obj->create('Magento\Catalog\Model\ProductFactory')->create();
    $product->setSku($data['sku']);
    $product->setName($data['name']);
    $product->setAttributeSetId($data['attr_set']);
    $product->setStatus(1);
    $product->setWeight(1);
    $product->setVisibility(4);
    $product->setTaxClassId($data['tax']);
    $product->setTypeId('simple');
    $product->setPrice('17.90');
    $product->setStockData(
        array(
            'use_config_manage_stock' => 0,
            'manage_stock' => 1,
            'is_in_stock' => 1,
            'qty' => 999999
        )
    );
    $product->save();
    $i = 0;
    $options = [];
    foreach ($data['options'] as $prescription => $values) {
        if (is_array($values) && !empty($values)) {
            $valuesData = [];
            foreach ($values as $value) {
                $valuesData[] = [
                    'title' => $value,
                    'price' => '0',
                    'price_type' => 'fixed',
                    'sku' => $prescription.'_'.$value,
                    'is_delete' => '0',
                ];
            }
            $options[$prescription] = [
                'sort_order' => $i,
                'title' => $prescription,
                'price_type' => 'fixed',
                'price' => '0',
                'type' => 'drop_down',
                'is_require' => '1',
                'values' => $valuesData
            ];
        }
        $i++;
    }
    $options['eye_side'] = [
        "sort_order"    => $i,
        "title"         => "Eye Side",
        "price_type"    => "fixed",
        "price"         => "",
        "type"          => "field",
        "is_require"    => 0
    ];
    foreach ($options as $arrayOption) {
        $option = $obj->create('\Magento\Catalog\Model\Product\OptionFactory')->create()
            ->setProductId($product->getId())
            ->setStoreId($product->getStoreId())
            ->addData($arrayOption);
        $option->save();
        $product->addOption($option);
    }
}