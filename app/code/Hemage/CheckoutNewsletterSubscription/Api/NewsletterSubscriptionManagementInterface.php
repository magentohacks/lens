<?php
/**
 * Copyright © 2018 Stämpfli AG. All rights reserved.
 * @author marcel.hauri@Hemage.com
 */

namespace Hemage\CheckoutNewsletterSubscription\Api;

use Hemage\CheckoutNewsletterSubscription\Api\Data\NewsletterSubscriptionInterface;

/**
 * Interface to set the newsletter subscription for logged in users
 * @api
 */
interface NewsletterSubscriptionManagementInterface
{
    /**
     * @param string $cartId
     * @param NewsletterSubscriptionInterface $newsletterSubscription
     * @return void
     */
    public function subscribe($cartId, NewsletterSubscriptionInterface $newsletterSubscription);
}
