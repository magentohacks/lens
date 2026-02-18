<?php

namespace BoostMyShop\Rma\Model\Rma;

use Magento\Sales\Model\Order\Email\Sender\CreditmemoSender;

class Refund
{
    protected $creditmemoLoader;
    protected $creditMemoManagement;
    protected $creditmemoSender;
    protected $_transaction;

    public function __construct(
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader,
        \Magento\Sales\Api\CreditmemoManagementInterface $creditMemoManagement,
        CreditmemoSender $creditmemoSender,
        \Magento\Framework\DB\Transaction $transaction
    ){
        $this->creditmemoLoader = $creditmemoLoader;
        $this->creditMemoManagement = $creditMemoManagement;
        $this->_transaction = $transaction;
        $this->creditmemoSender = $creditmemoSender;
    }

    public function process($rma, $data)
    {

        $this->creditmemoLoader->setOrderId($rma->getOrder()->getId());
        $creditmemo = $this->creditmemoLoader->load();
        if (!$creditmemo)
            throw new \Exception('Unable to create credit memo.');

        $creditmemo->addComment($data['comment_text'], false, false);

        $this->creditMemoManagement->refund($creditmemo, (bool)$data['do_offline'], true);

        $this->creditmemoSender->send($creditmemo);

        return $creditmemo;
    }

}