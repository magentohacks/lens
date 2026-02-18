<?php
namespace Lens\Manager\Model\Form\Source;

class DiameterPrescriptions implements \Magento\Framework\Data\OptionSourceInterface
{
    public $helper;
    public function __construct(
        \Lens\Manager\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array("value" => "<value>", "label"=> "<label>"), ...)
     */
    public function toOptionArray()
    {
        /**
         * @var $questionCollection \Namespace\ModuleName\Model\ResourceModel\Questions\Collection
         */
        $options = [];
        if ($this->helper->getDiameterValue()) {
            $axisPrescriptions = explode(',', $this->helper->getDiameterValue());
            foreach ($axisPrescriptions as $axis) {
                $options[] = [
                    'label' => $axis,
                    'value' => $axis
                ];
            }
        }
        return $options;
    }
}