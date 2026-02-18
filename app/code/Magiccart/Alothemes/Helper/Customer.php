<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2025-05-24 18:48:40
 * @@Modify Date: 2025-05-24 20:34:17
 * @@Function:
 */

namespace Magiccart\Alothemes\Helper;

class Customer extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $httpContext;

    public function __construct(
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
        )
    {
        $this->httpContext = $httpContext;
    }
    public function isLoggedIn()
    {
        return (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }
    public function getGroup()
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP);
    }
}
