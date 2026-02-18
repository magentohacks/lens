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

namespace Magestore\Pdfinvoiceplus\Model\PdfTemplateRender;

/**
 * Class VariableCollector
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class VariableCollector extends \Magento\Framework\DataObject
{
    /**
     * @var \Magento\Sales\Model\Order\Address\Renderer
     */
    protected $_addressRenderer;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_stdTimeZone;

    /**
     * VariableCollector constructor.
     *
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $stdTimeZone
     * @param array $data
     */
    public function __construct(
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $stdTimeZone,
        array $data = []
    )
    {
        parent::__construct($data);
        $this->_addressRenderer = $addressRenderer;
        $this->_objectManager = $objectManager;
        $this->_stdTimeZone = $stdTimeZone;
    }

    /**
     * @param null $date
     * @param int $format
     * @param bool $showTime
     */
    protected function _formatDate($date = null, $locale = null, $useTimezone = true)
    {

        $date = $this->_stdTimeZone->date($date, $locale, $useTimezone);

        return $date->format('Y-m-d_H-i-s');
    }


    /**
     * @return array
     */
    public function getOrderVariables()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getOrder();

        $variables = [];
        foreach ($order->getData() as $key => $value) {
            $variables['order_' . $key] = ['value' => $value];
            switch ($key) {
                case 'grand_total':
                    $variables['order_' . $key] = ['value' => $order->formatPriceTxt($order->getGrandTotal())];
                    break;
                case 'shipping_amount':
                    $variables['order_' . $key] = ['value' => $order->formatPriceTxt($order->getShippingAmount())];
                    break;
                case 'discount_amount':
                    $variables['order_' . $key] = ['value' => $order->formatPriceTxt($order->getDiscountAmount())];
                    break;
                case 'tax_amount':
                    $variables['order_' . $key] = ['value' => $order->formatPriceTxt($order->getTaxAmount())];
                    break;
                case 'subtotal':
                    $variables['order_' . $key] = ['value' => $order->formatPriceTxt($order->getSubtotal())];
                    break;
                case 'created_at':
                    $variables['order_' . $key] = ['value' => $this->_formatDate($order->getCreatedAt())];
                    break;
                case 'subtotal_invoiced':
                    $variables['order_' . $key] = ['value' => $order->formatPriceTxt($order->getSubtotalInvoiced())];
                    break;
                case 'subtotal_refunded':
                    $variables['order_' . $key] = ['value' => $order->formatPriceTxt($order->getSubtotalRefunded())];
                    break;
                case 'tax_refunded':
                    $variables['order_' . $key] = ['value' => $order->formatPriceTxt($order->getTaxRefunded())];
                    break;
                case 'total_paid':
                    $variables['order_' . $key] = ['value' => $order->formatPriceTxt($order->getTotalPaid())];
                    break;
                default:
                    $variables['order_' . $key] = ['value' => $value];
                    break;
            }
        }

        return $variables;
    }

    /**
     * @return array
     */
    public function getInvoiceVariables()
    {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $this->getData('invoice');

        $variables = [];
        if ($invoice) {
            $order = $invoice->getOrder();
            foreach ($invoice->getData() as $key => $value) {
                switch ($key) {
                    case 'order_id':
                        $variables['invoice_' . $key] = ['value' => $order->getIncrementId()];
                        break;
                    case 'grand_total':
                        $variables['invoice_' . $key] = ['value' => $order->formatPriceTxt($invoice->getGrandTotal())];
                        break;
                    case 'shipping_amount':
                        $variables['invoice_' . $key] = ['value' => $order->formatPriceTxt($invoice->getShippingAmount())];
                        break;
                    case 'discount_amount':
                        $variables['invoice_' . $key] = ['value' => $order->formatPriceTxt($order->getDiscountAmount())];
                        break;
                    case 'tax_amount' :
                        $variables['invoice_' . $key] = ['value' => $order->formatPriceTxt($invoice->getTaxAmount())];
                        break;
                    case 'subtotal':
                        $variables['invoice_' . $key] = ['value' => $order->formatPriceTxt($invoice->getSubtotal())];
                        break;
                    case 'created_at' :
                        $variables['invoice_' . $key] = ['value' => $this->_formatDate($invoice->getCreatedAt())];
                        break;
                    case 'subtotal_invoiced':
                        $variables['invoice_' . $key] = ['value' => $order->formatPriceTxt($order->getSubtotalInvoiced())];
                        break;
                    case 'subtotal_refunded':
                        $variables['invoice_' . $key] = ['value' => $order->formatPriceTxt($order->getSubtotalRefunded())];
                        break;
                    case 'tax_refunded':
                        $variables['invoice_' . $key] = ['value' => $order->formatPriceTxt($order->getTaxRefunded())];
                        break;
                    case 'total_paid':
                        $variables['invoice_' . $key] = ['value' => $order->formatPriceTxt($order->getTotalPaid())];
                        break;
                    case 'state':
                        switch ($value) {
                            case 1:
                                $variables['invoice_' . $key] = ['value' => __('Pending')];
                                break;
                            case 2:
                                $variables['invoice_' . $key] = ['value' => __('Paid')];
                                break;
                            case 3:
                                $variables['invoice_' . $key] = ['value' => __('Closed')];
                                break;
                        }
                        break;
                    default:
                        $variables['invoice_' . $key] = ['value' => $value];
                        break;
                }
            }
        }

        return $variables;
    }

    /**
     * @return array
     */
    public function getCreditmemoVariables()
    {
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = $this->getData('creditmemo');
        $variables = [];
        if ($creditmemo) {
            $order = $creditmemo->getOrder();
            foreach ($creditmemo->getData() as $key => $value) {
                switch ($key) {
                    case 'grand_total':
                        $variables['creditmemo_' . $key] = ['value' => $order->formatPriceTxt($creditmemo->getGrandTotal())];
                        break;
                    case 'order_id' :
                        $variables['creditmemo_' . $key] = ['value' => $order->getIncrementId()];
                        break;
                    case 'shipping_amount' :
                        $variables['creditmemo_' . $key] = ['value' => $order->formatPriceTxt($creditmemo->getShippingAmount())];
                        break;
                    case 'discount_amount':
                        $variables['creditmemo_' . $key] = ['value' => $order->formatPriceTxt($order->getDiscountAmount())];
                        break;
                    case 'tax_amount':
                        $variables['creditmemo_' . $key] = ['value' => $order->formatPriceTxt($creditmemo->getTaxAmount())];
                        break;
                    case 'subtotal':
                        $variables['creditmemo_' . $key] = ['value' => $order->formatPriceTxt($creditmemo->getSubtotal())];
                        break;
                    case 'created_at':
                        $variables['creditmemo_' . $key] = ['value' => $this->_formatDate($creditmemo->getCreatedAt())];
                        break;
                    case 'subtotal_invoiced':
                        $variables['creditmemo_' . $key] = ['value' => $order->formatPriceTxt($order->getSubtotalInvoiced())];
                        break;
                    case 'subtotal_refunded':
                        $variables['creditmemo_' . $key] = ['value' => $order->formatPriceTxt($order->getSubtotalRefunded())];
                        break;
                    case 'tax_refunded':
                        $variables['creditmemo_' . $key] = ['value' => $order->formatPriceTxt($order->getTaxRefunded())];
                        break;
                    case 'total_paid':
                        $variables['creditmemo_' . $key] = ['value' => $order->formatPriceTxt($order->getTotalPaid())];
                        break;
                    case 'state' :
                        switch ($value) {
                            case 1:
                                $variables['creditmemo_state'] = ['value' => __('Pending')];
                                break;
                            case 2:
                                $variables['creditmemo_state'] = ['value' => __('Paid')];
                                break;
                            case 3:
                                $variables['creditmemo_state'] = ['value' => __('Closed')];
                                break;
                        }
                        break;
                    default:
                        $variables['creditmemo_' . $key] = ['value' => $value];
                        break;
                }
            }
        }

        return $variables;
    }

    /**
     * @return array
     */
    public function getShipmentVariables()
    {
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $this->getData('shipment');

        /** @var \Magento\Sales\Model\Order $order */
        $order = $shipment->getOrder();
        $variables = [];
        if ($shipment) {
            foreach ($shipment->getData() as $key => $value) {
                switch ($key) {
                    case 'order_id' :
                        $variables['shipment_' . $key] = ['value' => $order->getIncrementId()];
                        break;
                    case 'created_at':
                        $variables['shipment_' . $key] = ['value' => $this->_formatDate($shipment->getCreatedAt())];
                        break;
                    default:
                        $variables['shipment_' . $key] = ['value' => $value];
                        break;
                }
            }
        }

        $variables['shipment_total_charge'] = ['value' => $order->formatPriceTxt($order->getShippingAmount())];

        return $variables;
    }



    /**
     * @return array
     */
    public function getCustomerVariables()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getOrder();
        $customerId = $order->getCustomerId();
        /** @var \Magento\Customer\Model\Customer $customerModel */
        $customerModel = $this->_objectManager->create('Magento\Customer\Model\Customer');
        $customer = $customerModel->load($customerId);

        $variables = [];

        foreach ($customer->getData() as $key => $value) {
            switch ($key) {
                case 'created_at':
                    $variables['customer_' . $key] = ['value' => $this->_formatDate($customer->getData('created_at'))];
                    break;
                case 'created_in':
                    $variables['customer_' . $key] = ['value' => $customer->getData('created_in')];
                    break;
                case 'dbo' :
                    $variables['customer_' . $key] = ['value' => $this->_formatDate($customer->getData('dbo'))];
                    break;
                default:
                    $variables['customer_' . $key] = ['value' => $value];
                    break;
            }
        }

        return $variables;
    }

    /**
     * @param $type
     *
     * @return array
     */
    public function getAddressInfo($type)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getOrder();
        // reformat billing address
        $billingInfo = $this->_addressRenderer->format($order->getBillingAddress(), 'html');

        if ($order->getShippingAddress()) {
            $shippingInfo = $this->_addressRenderer->format($order->getShippingAddress(), 'html');
        } else {
            $shippingInfo = '';
        }

        $variables = [
            $type . '_billing_address' => [
                'value' => $billingInfo,
                'label' => __('Billing Address'),
            ],
            $type . '_shipping_address' => [
                'value' => $shippingInfo,
                'label' => __('Shipping Address'),
            ]
        ];

        return $variables;
    }

    /**
     * @param $type
     *
     * @return array
     */
    public function getPaymentInfo($type)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getOrder();
        $paymentInfo = $order->getPayment()->getMethodInstance()->getTitle();

        $variables = [
            $type . '_payment_method' => [
                'value' => $paymentInfo,
                'label' => __('Billing Method'),
            ],
            $type . '_billing_method_currency' => [
                'value' => $order->getOrderCurrencyCode(),
                'label' => __('Order was placed using'),
            ],
        ];

        return $variables;
    }

    /**
     * @param $type
     *
     * @return array
     */
    public function getShippingInfo($type)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getOrder();

        if ($order->getShippingDescription()) {
            $shippingInfo = $order->getShippingDescription();
        } else {
            $shippingInfo = '';
        }

        $variables = [
            $type . '_shipping_method' => [
                'value' => $shippingInfo,
                'label' => __('Shipping Information'),
            ]
        ];

        return $variables;
    }

    /**
     * @return array
     */
    public function getInfoMergedVariables()
    {
        $type = $this->getData('type');

        $vars = array_merge(
            $this->getOrderVariables()
            , $this->getCustomerVariables()
            , $this->getAddressInfo($type)
            , $this->getPaymentInfo($type)
            , $this->getShippingInfo($type)
        );

        switch ($type) {
            case 'invoice':
                $vars = array_merge($vars, $this->getInvoiceVariables());
                break;
            case 'creditmemo':
                $vars = array_merge($vars, $this->getCreditmemoVariables());
                break;
            case 'shipment':
                $vars = array_merge($vars, $this->getShipmentVariables());
                break;
        }

        return $this->arrayToStandard($vars);
    }

    /**
     * @param array $variable
     *
     * @return array
     */
    protected function arrayToStandard($variable = [])
    {
        $variables = [];
        foreach ($variable as $key => $var) {
            $variables[] = [$key => $var];
        }

        return $variables;
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getData('order');
    }
}