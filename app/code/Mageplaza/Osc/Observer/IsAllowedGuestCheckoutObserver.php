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
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) 2017-2018 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Osc\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Downloadable\Model\ResourceModel\Link\CollectionFactory as LinkCollectionFactory;
use Magento\Framework\Event\ObserverInterface;
use Mageplaza\Osc\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CheckoutSubmitBefore
 * @package Mageplaza\Osc\Observer
 */
class IsAllowedGuestCheckoutObserver extends \Magento\Downloadable\Observer\IsAllowedGuestCheckoutObserver implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $_helper;

    /**
     * IsAllowedGuestCheckoutObserver constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Data $helper
     */
    public function __construct(
        LinkCollectionFactory $linkCollectionFactory,
        ScopeConfigInterface $scopeConfig,
        Data $helper,
        StoreManagerInterface $storeManager
    )
    {
        $this->_helper = $helper;
        parent::__construct($scopeConfig, $linkCollectionFactory, $storeManager);
    }

    /**
     * @inheritdoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_helper->isEnabled()) {
            return $this;
        }

        return parent::execute($observer);
    }
}
