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
namespace Mageplaza\QuickbooksOnline\Controller\Index;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Index
 * @package Mageplaza\QuickbooksOnline\Controller\Index
 */
class Index extends Callback
{
    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        if ($this->session->getMpQuickbooksErrorMessage()) {
            printf('<b style="color:red">' . $this->session->getMpQuickbooksErrorMessage() . '</b>');
            $this->session->setMpQuickbooksErrorMessage('');
        }

        if ($this->session->getMpQuickbooksSuccessMessage()) {
            printf('<b style="color:green">' . $this->session->getMpQuickbooksSuccessMessage() . '</b>');
            $this->session->setMpQuickbooksSuccessMessage('');
        }
    }
}
