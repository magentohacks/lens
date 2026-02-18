<?php
/**
 * Copyright © 2018 Stämpfli AG. All rights reserved.
 * @author marcel.hauri@Hemage.com
 */

namespace Hemage\CheckoutNewsletterSubscription\Api\Data;

interface NewsletterSubscriptionInterface
{
    /**
     * @param string $subscribe
     * @return null
     */
    public function setSubscribe($subscribe);

    /**
     * @return string
     */
    public function getSubscribe();
}
