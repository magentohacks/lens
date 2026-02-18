<?php

namespace BoostMyShop\OrderPreparation\Model;


class CarrierTemplate extends \Magento\Framework\Model\AbstractModel
{
    const kTypeOrderDetailsExport = 'order_details_export';
    const kTypeSimpleAddressLabel = 'simple_address_label';

    protected $_rendererOrderDetailExport;
    protected $_rendererSimpleAddressLabel;
    protected $_csvTrackingExtractHandler;
    protected $_inProgressFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \BoostMyShop\OrderPreparation\Model\CarrierTemplate\Renderer\OrderDetailsExport $rendererOrderDetailExport,
        \BoostMyShop\OrderPreparation\Model\CarrierTemplate\Renderer\SimpleAddressLabel $rendererSimpleAddressLabel,
        \BoostMyShop\OrderPreparation\Model\CarrierTemplate\Extract\CsvTrackingExtractHandler $csvTrackingExtractHandler,
        \BoostMyShop\OrderPreparation\Model\InProgressFactory $inProgressFactory,
        array $data = []
    )
    {
        $this->_rendererOrderDetailExport = $rendererOrderDetailExport;
        $this->_rendererSimpleAddressLabel = $rendererSimpleAddressLabel;
        $this->_csvTrackingExtractHandler = $csvTrackingExtractHandler;
        $this->_inProgressFactory = $inProgressFactory;

        parent::__construct($context, $registry, null, null, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('BoostMyShop\OrderPreparation\Model\ResourceModel\CarrierTemplate');
    }

    /**
     *
     */
    public function getShippingLabelFile($ordersInProgress)
    {
        $renderer = null;

        switch($this->getct_type())
        {
            case self::kTypeOrderDetailsExport:
                $renderer = $this->_rendererOrderDetailExport;
                break;
            case self::kTypeSimpleAddressLabel:
                $renderer = $this->_rendererSimpleAddressLabel;
                break;
        }

        if (!$renderer)
            throw new \Exception('No renderer available for type shipping label template "'.$this->getct_type().'"');
        else {
            $ordersInProgress = $this->filterOrdersInProgress($ordersInProgress);
            return $renderer->getShippingLabelFile($ordersInProgress, $this);
        }

    }

    public function filterOrdersInProgress($ordersInProgress)
    {
        $orders = [];

        foreach($ordersInProgress as $orderInProgress)
        {
            if (!$orderInProgress->getShipment())
                continue;
            if (!$this->shippingMethodSupported($orderInProgress->getshipping_method()))
                continue;
            $orders[] = $orderInProgress;
        }

        return $orders;
    }

    public function shippingMethodSupported($code)
    {
        $supportedMethods = unserialize($this->getct_shipping_methods());
        return in_array($code, $supportedMethods);
    }

    public function importTracking($fileContent)
    {
        $stats = ['success' => 0, 'error' => 0];
        $datas = $this->_csvTrackingExtractHandler->extract($fileContent, $this);
        foreach($datas as $data)
        {
            try
            {
                if (!$data['shipment'] || !$data['tracking'])
                    throw new \Exception('Data missing');

                $inProgress = $this->_inProgressFactory->create()->loadByShipmentReference($data['shipment']);
                if (!$inProgress->getId())
                    throw new \Exception('Can not find order in progress');

                $inProgress->addTracking($data['tracking']);
                $stats['success']++;
            }
            catch(\Exception $ex)
            {

                $stats['error']++;
            }
        }

        return $stats;
    }

}