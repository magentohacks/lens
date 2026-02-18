<?php

namespace BoostMyShop\Rma\Block\Rma\Renderer\SelectOrder;

use Magento\Framework\DataObject;

class Products extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $_orderItemCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Context $context,
                                \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory,
                                array $data = [])
    {
        parent::__construct($context, $data);

        $this->_orderItemCollectionFactory = $orderItemCollectionFactory;
    }

    public function render(DataObject $order)
    {
        $html = [];

        $collection = $this->getCollection($order);
        foreach ($collection as $item) {
            $html[] .= $this->renderItem($order, $item);
        }

        return implode('<br>', $html);
    }

    public function getCollection($order)
    {
        $att = ['qty_canceled', 'qty_ordered', 'qty_refunded', 'qty_shipped', 'name'];
        $collection = $this->_orderItemCollectionFactory->create()->setOrderFilter($order->getentity_id());
        foreach($att as $item)
            $collection->addAttributeToSelect($item);
        return $collection;
    }

    public function renderItem($order, $item)
    {
        $qty = $item->getQtyOrdered();
        return ((int)$qty).'x '.$item->getName();
    }
}