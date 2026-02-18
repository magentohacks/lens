<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Pdfinvoiceplus\Controller\Adminhtml\PrintPdf;

/**
 * class MassPrintCreditmemo
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class MassPrintCreditmemo extends AbstractMassPrintAction
{
    /**
     * @return mixed
     */
    public function getRenderingCollection()
    {
        if ($this->getRequest()->getParam('namespace') == 'sales_order_creditmemo_grid') {
            return $this->filter->getCollection($this->_creditmemoCollectionFactory->create());
        } else {
            /** @var \Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection $creditmemoCollection */
            $creditmemoCollection = $this->_creditmemoCollectionFactory->create();

            /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $orderCollection */
            $orderCollection = $this->filter->getCollection($this->_orderCollectionFactory->create());

            $creditmemoIds = [];
            foreach ($orderCollection as $order) {
                /** @var \Magento\Sales\Model\Order $order */
                $creditmemoIds = array_merge($creditmemoIds, $order->getCreditmemosCollection()->getAllIds());
            }

            $creditmemoCollection->addFieldToFilter('entity_id', ['in' => $creditmemoIds]);

            return $creditmemoCollection;
        }
    }

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\Collection $collection
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\VersionControl\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareRenderingCollectionBeforePrint(
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Collection $collection
    ) {
        if (!$collection->count()) {
            throw  new \Magento\Framework\Exception\LocalizedException(__('You have no creditmemos PDF file.'));
        }

        return $collection;
    }
}