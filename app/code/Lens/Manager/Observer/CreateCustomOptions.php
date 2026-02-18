<?php

namespace Lens\Manager\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\Product\OptionFactory;

class CreateCustomOptions implements ObserverInterface
{
    const PRES_ARRAY = [
        'addition'  => "Addition",
        'axis'      => "Axis",
        'cylinder'  => "Cylinder",
        'diameter'  => "Diameter",
        'dominance' => "Dominance",
        'basecurve' => "Basecurve",
        'power'     => "Power"
    ];
    
    /**
     * Http Request
     *
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;
    public $option;

    /**
     * @param \Magento\Framework\App\Request\Http $request
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        OptionFactory $option,
        array $data = []
    ) {
        $this->option = $option;
        $this->request = $request;
    }

    /**
     *
     *  @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();
        $params = $this->request->getParams();
        $options = [];
        if ($product->getOptions()) {
            foreach ($product->getOptions() as $option) {
                if ($option->getType() == 'field') {
                    $option->delete();
                }
            }
        }
        if (isset($params['prescriptions']) &&!empty($params['prescriptions'])) {
            $pescriptions = $params['prescriptions'];
            $i = 0;
            foreach ($pescriptions as $prescription => $values) {
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
                        'title' => self::PRES_ARRAY[$prescription],
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
                $option = $this->option->create()
                    ->setProductId($product->getId())
                    ->setStoreId($product->getStoreId())
                    ->addData($arrayOption);
                $option->save();
                $product->addOption($option);
            }
        }
    }
}