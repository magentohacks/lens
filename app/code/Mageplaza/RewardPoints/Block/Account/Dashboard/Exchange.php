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
 * @package     Mageplaza_RewardPoints
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\RewardPoints\Block\Account\Dashboard;

use Mageplaza\RewardPoints\Block\Account\Dashboard;

/**
 * Class Exchange
 * @package Mageplaza\RewardPoints\Block\Account\Dashboard
 */
class Exchange extends Dashboard
{
    /**
     * @return bool
     */
    public function canDisplay()
    {
        return $this->getEarningRate() || $this->getSpendingRate() || $this->getMaxPointPerCustomer() || $this->getPointExpired();
    }

    /**
     * Get max point per customer
     * @return int
     */
    public function getMaxPointPerCustomer()
    {
        return $this->helper->getMaxPointPerCustomer();
    }

    /**
     * Get point expired
     * @return mixed
     */
    public function getPointExpired()
    {
        $expired = $this->helper->getSalesPointExpiredAfter();
        if (empty($expired)) {
            return false;
        }

        return $expired > 1 ? __('%1 days', $expired) : __('%1 day', $expired);
    }

    /**
     * @return \Mageplaza\RewardPoints\Model\Rate | null
     */
    public function getEarningRate()
    {
        $rate = $this->helper->getCalculationHelper()->getEarningRate();
        if (!$rate->isValid()) {
            return null;
        }

        return $rate;
    }

    /**
     * @return \Mageplaza\RewardPoints\Model\Rate | null
     */
    public function getSpendingRate()
    {
        $rate = $this->helper->getCalculationHelper()->getSpendingRate();
        if (!$rate->isValid()) {
            return null;
        }

        return $rate;
    }
}