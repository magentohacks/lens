<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_QuickbooksOnline
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\QuickbooksOnline\Controller\Adminhtml\Invoice;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Model\Order\Invoice;
use Mageplaza\QuickbooksOnline\Controller\Adminhtml\AbstractMassAction;
use Mageplaza\QuickbooksOnline\Model\Source\QuickbooksModule;

/**
 * Class Add
 * @package Mageplaza\QuickbooksOnline\Controller\Adminhtml\Invoice
 */
class Add extends AbstractMassAction
{
    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('invoice_id');

        return $this->addToQueue($id);
    }

    /**
     * @return Invoice|mixed
     */
    public function getModel()
    {
        return $this->invoiceFactory->create();
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return 'sales/invoice/view';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return QuickbooksModule::INVOICE;
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function getParamUrl($id)
    {
        return ['invoice_id' => $id];
    }
}
