<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_RewardPointsUltimate
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\RewardPointsUltimate\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Mageplaza\RewardPointsUltimate\Helper\Data as HelperData;
use Mageplaza\RewardPointsUltimate\Model\BehaviorFactory;
use Mageplaza\RewardPointsUltimate\Model\InvitationFactory;
use Mageplaza\RewardPointsUltimate\Model\Source\CustomerEvents;

/**
 * Class QuoteSubmitSuccess
 * @package Mageplaza\RewardPointsUltimate\Observer
 */
class QuoteSubmitSuccess implements ObserverInterface
{
    /**
     * @var \Mageplaza\RewardPointsUltimate\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Mageplaza\RewardPointsUltimate\Model\BehaviorFactory
     */
    protected $behaviorFactory;

    /**
     * @var InvitationFactory
     */
    protected $invitationFactory;

    /**
     * QuoteSubmitSuccess constructor.
     * @param HelperData $helperData
     * @param Session $customerSession
     * @param BehaviorFactory $behaviorFactory
     * @param InvitationFactory $invitationFactory
     */
    public function __construct(
        HelperData $helperData,
        Session $customerSession,
        BehaviorFactory $behaviorFactory,
        InvitationFactory $invitationFactory
    )
    {
        $this->helperData        = $helperData;
        $this->customerSession   = $customerSession;
        $this->behaviorFactory   = $behaviorFactory;
        $this->invitationFactory = $invitationFactory;
    }

    /**
     * @param EventObserver $observer
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        /**
         * Save invited to history and delete refer cookie
         */
        if ($quote->getMpRewardReferralEarn()) {
            $invitation    = $this->invitationFactory->create();
            $referralEmail = $this->helperData->getAccountHelper()
                ->getCustomerById($quote->getMpRewardReferralId())
                ->getEmail();
            $invitation->setReferralEmail($referralEmail)
                ->setReferralEarn($quote->getMpRewardReferralEarn())
                ->setInvitedEmail($quote->getCustomerEmail())
                ->setInvitedEarn($quote->getInvitedEarn())
                ->setInvitedDiscount($quote->getMpRewardInvitedDiscount())
                ->setStoreId($quote->getStoreId())
                ->save();

            /**
             * Delete refer cookie
             */
            $this->helperData->getCookieHelper()->deleteMpRefererKeyFromCookie();
        }

        /**
         * Save product id purchased and create transaction sell point
         */
        if ($quote->getCustomerId()) {
            $behavior           = $this->behaviorFactory->create()
                ->getBehaviorRuleByAction(
                    CustomerEvents::PRODUCT_REVIEW,
                    true,
                    $quote->getCustomer()->getGroupId()
                );
            $mpRewardSellPoints = 0;
            if ((is_array($quote->getItems()) || is_object($quote->getItems())) && count($quote->getItems())) {
                foreach ($quote->getItems() as $item) {
                    if ($behavior->getId() && !$item->getParentItemId()) {
                        $mpRewardPurchased   = $this->customerSession->getMpRewardPurchased();
                        $mpRewardPurchased[] = $item->getProductId();
                        $this->customerSession->setMpRewardPurchased($mpRewardPurchased);
                    }
                    if ($item->getMpRewardSellPoints()) {
                        $mpRewardSellPoints += ($item->getMpRewardSellPoints() * $item->getQty());
                    }
                }

                if ($mpRewardSellPoints > 0) {
                    $this->helperData->addTransaction(
                        HelperData::ACTION_SELL_POINTS,
                        $quote->getCustomer(),
                        -$mpRewardSellPoints,
                        $order
                    );
                }
            }
        }
    }
}
