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

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Session\SessionManagerInterface;
use Mageplaza\QuickbooksOnline\Helper\Data as HelperData;
use Laminas\Http\Request;

/**
 * Class Callback
 * @package Mageplaza\QuickbooksOnline\Controller\Index
 */
class Callback extends Action
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var SessionManagerInterface
     */
    protected $session;

    /**
     * Callback constructor.
     *
     * @param Context $context
     * @param HelperData $helperData
     * @param SessionManagerInterface $session
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        SessionManagerInterface $session
    ) {
        parent::__construct($context);
        $this->helperData = $helperData;
        $this->session    = $session;
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $code    = $this->getRequest()->getParam('code');
        $realmId = $this->getRequest()->getParam('realmId');

        if ($code) {
            $clientId = $this->helperData->getClientId();

            if (!$clientId) {
                $this->session->setMpQuickbooksErrorMessage('Please fill Client Id!');

                return $this->_redirect('mpquickbooks/');
            }

            $clientSecret = $this->helperData->getClientSecret();

            if (!$clientSecret) {
                $this->session->setMpQuickbooksErrorMessage('Please fill Client Secret');

                return $this->_redirect('mpquickbooks/');
            }

            $redirectURI = $this->helperData->getAuthUrl();

            if (!$redirectURI) {
                $this->session->setMpQuickbooksErrorMessage(__('Please fill Authorized redirect URIs'));

                return $this->_redirect('mpquickbooks/');
            }

            $params = [
                'grant_type'   => 'authorization_code',
                'code'         => $code,
                'redirect_uri' => $redirectURI
            ];

            try {
                $resp = $this->helperData->requestAccess(
                    Request::METHOD_POST,
                    http_build_query($params)
                );

                if ($resp && is_array($resp) && isset($resp['access_token'])) {
                    $this->helperData->saveConfig('company_id', $realmId);
                    $this->helperData->saveAPIData($resp);
                    $this->helperData->saveCompanyInfo($realmId);
                    $this->helperData->createSyncAssetAccountProduct($realmId);
                    $this->helperData->createTaxAgency($realmId);
                    $this->helperData->createCustomerGuest($realmId);
                    $this->session->setMpQuickbooksSuccessMessage(
                        __('The Access Token has been saved successfully. You can close this window, clear the cache and then refresh configuration page.')
                    );
                } else {
                    $this->session->setMpQuickbooksErrorMessage(__('Invalid access token. Please try again!'));
                }
            } catch (Exception $e) {
                $this->session->setMpQuickbooksErrorMessage($e->getMessage());
            }
        } else {
            $this->session->setMpQuickbooksErrorMessage(__('Grant token not found!'));
        }

        return $this->_redirect('mpquickbooks/');
    }
}
