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
namespace Mageplaza\QuickbooksOnline\Helper;

use Magento\Config\Model\ResourceModel\Config as ModelConfig;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory as ConfigCollectionFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Payment\Helper\Data as HelperPayment;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\QuickbooksOnline\Model\Sync;
use Laminas\Http\Request;
use Laminas\Http\Response;

/**
 * Class Data
 * @package Mageplaza\QuickbooksOnline\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'mpquickbooks';
    const TOKEN_URL          = 'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer/';

    /**
     * @var CurlFactory
     */
    protected $curlFactory;

    /**
     * @var ConfigCollectionFactory
     */
    protected $configCollectionFactory;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var ModelConfig
     */
    protected $modelConfig;

    /**
     * @var HelperPayment
     */
    protected $helperPayment;

    /**
     * @var Sync
     */
    protected $sync;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param CurlFactory $curlFactory
     * @param ConfigCollectionFactory $configCollectionFactory
     * @param EncryptorInterface $encryptor
     * @param ModelConfig $modelConfig
     * @param HelperPayment $helperPayment
     * @param Sync $sync
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        CurlFactory $curlFactory,
        ConfigCollectionFactory $configCollectionFactory,
        EncryptorInterface $encryptor,
        ModelConfig $modelConfig,
        HelperPayment $helperPayment,
        Sync $sync
    ) {
        $this->curlFactory             = $curlFactory;
        $this->configCollectionFactory = $configCollectionFactory;
        $this->encryptor               = $encryptor;
        $this->modelConfig             = $modelConfig;
        $this->helperPayment           = $helperPayment;
        $this->sync                    = $sync;
        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @return int
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getScopeUrl()
    {
        $scope = $this->_request->getParam(ScopeInterface::SCOPE_STORE) ?: $this->storeManager->getStore()->getId();

        if ($website = $this->_request->getParam(ScopeInterface::SCOPE_WEBSITE)) {
            $scope = $this->storeManager->getWebsite($website)->getDefaultStore()->getId();
        }

        return $scope;
    }

    /**
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getAuthUrl()
    {
        $storeId = $this->getScopeUrl();
        /**
         * @var Store $store
         */
        $store = $this->storeManager->getStore($storeId);

        return $this->_getUrl(
            'mpquickbooks/index/callback',
            [
                '_nosid'  => true,
                '_scope'  => $storeId,
                '_secure' => $store->isUrlSecure()
            ]
        );
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function decrypt($value)
    {
        return $this->encryptor->decrypt($value);
    }

    /**
     * @return mixed
     */
    public function getMode()
    {
        return $this->getConfigGeneral('environment');
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        $clientId = $this->getConfigGeneral('client_id');

        return $this->decrypt($clientId);
    }

    /**
     * @return string
     */
    public function getClientSecret()
    {
        $clientSecret = $this->getConfigGeneral('client_secret');

        return $this->decrypt($clientSecret);
    }

    /**
     * @return array|mixed
     */
    public function getAccessData()
    {
        $accessData = $this->getConfigGeneral('access_data');

        if ($accessData) {
            return self::jsonDecode($this->decrypt($accessData));
        }

        return [];
    }

    /**
     * @return mixed|string
     */
    public function getRefreshToken()
    {
        $accessData = $this->getAccessData();
        if (isset($accessData['refresh_token'])) {
            return $accessData['refresh_token'];
        }

        return '';
    }

    /**
     * @return string
     */
    public function getLastRequestToken()
    {
        $config = $this->configCollectionFactory->create()
            ->addFieldToFilter('path', $this->getQuickbooksGeneralPathByKey('last_request_token'))
            ->getFirstItem();

        if ($config->getId()) {
            return $config->getValue();
        }

        return '';
    }

    /**
     * @throws LocalizedException
     */
    public function checkRefreshAccessToken()
    {
        $lastRequestToken = (int) $this->getLastRequestToken();

        if ($lastRequestToken + 3600 < time()) {
            $refreshData = [
                'grant_type'    => 'refresh_token',
                'refresh_token' => $this->getRefreshToken()
            ];

            $resp = $this->requestAccess(
                Request::METHOD_POST,
                http_build_query($refreshData)
            );

            if (isset($resp['access_token'])) {
                $this->saveAPIData($resp, true);
            } else {
                throw new LocalizedException(__('Cannot refresh access token.'));
            }
        }
    }

    /**
     * @param string $method
     * @param string $params
     *
     * @return mixed|string
     */
    public function requestAccess($method, $params)
    {
        $headers = [
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic ' . $this->getAuthorization(),
            'Host: oauth.platform.intuit.com'
        ];

        return $this->processCurl($method, self::TOKEN_URL, $headers, $params);
    }

    /**
     * @param string $method
     * @param string $url
     * @param string $params
     *
     * @return mixed|string
     */
    public function requestData($method, $url, $params = '')
    {
        $headers = [
            'accept: application/json',
            'authorization: Bearer ' . $this->getAccessToken(),
            'content-type: application/json'
        ];

        if ($method === Request::METHOD_POST) {
            $params = self::jsonEncode($params);
        }

        return $this->processCurl($method, $url, $headers, $params);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $headers
     * @param string $params
     * @param string $http_ver
     *
     * @return mixed|string
     */
    public function processCurl($method, $url = '', $headers = [], $params = '', $http_ver = '1.1')
    {
        $httpAdapter = $this->curlFactory->create();

        if ($method === Request::METHOD_DELETE) {
            $httpAdapter->setOptions([CURLOPT_CUSTOMREQUEST => 'DELETE']);
        }

        $httpAdapter->write($method, $url, $http_ver, $headers, $params);
        $result   = $httpAdapter->read();
        $response = $this->extractBody($result);
        $response = self::jsonDecode($response);
        $httpAdapter->close();

        return $response;
    }

    /**
     * @return string
     */
    public function getAuthorization()
    {
        $clientId     = $this->getClientId();
        $clientSecret = $this->getClientSecret();

        return base64_encode($clientId . ':' . $clientSecret);
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        $config = $this->configCollectionFactory->create()
            ->addFieldToFilter('path', $this->getQuickbooksGeneralPathByKey('access_token'))
            ->getFirstItem();

        if ($config->getValue()) {
            return $this->decrypt($config->getValue());
        }

        return '';
    }

    /**
     * @param string $field
     *
     * @return string
     */
    public function getQuickbooksGeneralPathByKey($field)
    {
        return self::CONFIG_MODULE_PATH . '/general/' . $field;
    }

    /**
     * @param array $resp
     * @param bool $isRefreshToken
     */
    public function saveAPIData($resp, $isRefreshToken = false)
    {
        if ($isRefreshToken) {
            $resp['refresh_token'] = $this->getRefreshToken();
        }

        $accessToken = $this->encryptor->encrypt($resp['access_token']);
        $accessData  = $this->encryptor->encrypt(self::jsonEncode($resp));
        $this->saveConfig('access_token', $accessToken);
        $this->saveConfig('access_data', $accessData);
        $this->saveConfig('last_request_token', time());
    }

    /**
     * @param string $field
     * @param string $value
     */
    public function saveConfig($field, $value)
    {
        $path = $this->getQuickbooksGeneralPathByKey($field);
        $this->modelConfig->saveConfig($path, $value);
    }

    /**
     * @return mixed
     */
    public function getCompanyInfo()
    {
        return self::jsonDecode($this->getConfigGeneral('company_info'));
    }

    /**
     * @return mixed
     */
    public function getCompanyId()
    {
        return $this->getConfigGeneral('company_id');
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function getApiUrl($type)
    {
        return $this->getMode() . $this->getCompanyId() . '/' . $type . '?minorversion=41';
    }

    /**
     * @param string $url
     * @param string $method
     * @param string $params
     *
     * @return mixed|string
     * @throws LocalizedException
     */
    public function sendRequest($url, $method, $params = '')
    {
        $this->checkRefreshAccessToken();

        return $this->requestData($method, $url, $params);
    }

    /**
     * @param string $realmId
     */
    public function createTaxAgency($realmId)
    {
        $response = $this->requestData(
            Request::METHOD_POST,
            $this->getMode() . $realmId . '/taxagency?minorversion=41',
            ['DisplayName' => __('Tax Agency using for sync from Magento ') . time()]
        );

        if ($response && is_array($response) && isset($response['TaxAgency'])) {
            $this->saveConfig('tax_agency', $response['TaxAgency']['Id']);
        }
    }

    /**
     * @param string $realmId
     */
    public function createSyncAssetAccountProduct($realmId)
    {
        $params = [
            'BatchItemRequest' => [
                [
                    'bId'       => 'bid',
                    'operation' => 'create',
                    'Account'   => [
                        'Name'           => __('Asset Account by module Mageplaza_QuickbooksOnline ') . time(),
                        'AccountType'    => 'Other Current Asset',
                        'AccountSubType' => 'Inventory'
                    ]
                ],
                [
                    'bId'       => 'bid',
                    'operation' => 'create',
                    'Account'   => [
                        'Name'           => __('Expense Account by Mageplaza_QuickbooksOnline ') . time(),
                        'AccountType'    => 'Cost of Goods Sold',
                        'AccountSubType' => 'SuppliesMaterialsCogs'
                    ]
                ],
                [
                    'bId'       => 'bid',
                    'operation' => 'create',
                    'Account'   => [
                        'Name'           => __('Income Account by Mageplaza_QuickbooksOnline ') . time(),
                        'AccountType'    => 'Income',
                        'AccountSubType' => 'SalesOfProductIncome'
                    ]
                ]
            ]
        ];

        $response = $this->requestData(
            Request::METHOD_POST,
            $this->getMode() . $realmId . '/batch?minorversion=41',
            $params
        );

        if ($response && is_array($response) && isset($response['BatchItemResponse'])) {
            foreach ($response['BatchItemResponse'] as $item) {
                if (isset($item['Account']['Id'])) {
                    $id = $item['Account']['Id'];

                    switch ($item['Account']['Classification']) {
                        case 'Asset':
                            $this->saveConfig('asset_account', $id);
                            break;
                        case 'Expense':
                            $this->saveConfig('expense_account', $id);
                            break;
                        default:
                            $this->saveConfig('income_account', $id);
                            break;
                    }
                }
            }
        }
    }

    /**
     * @param string $realmId
     */
    public function saveCompanyInfo($realmId)
    {
        $companyInfo = $this->requestData(
            Request::METHOD_GET,
            $this->getMode() . $realmId . '/companyinfo/' . $realmId
        );

        $this->saveConfig('company_info', self::jsonEncode($companyInfo));
    }

    /**
     * @return mixed
     */
    public function getAssetAccount()
    {
        return $this->getConfigGeneral('asset_account');
    }

    /**
     * @return mixed
     */
    public function getExpenseAccount()
    {
        return $this->getConfigGeneral('expense_account');
    }

    /**
     * @return mixed
     */
    public function getIncomeAccount()
    {
        return $this->getConfigGeneral('income_account');
    }

    /**
     * @return mixed
     */
    public function getTaxAgency()
    {
        return $this->getConfigGeneral('tax_agency');
    }

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return array|mixed
     */
    public function getConfigSchedule($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . '/queue_schedule' . $code, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return array|mixed
     */
    public function getDeleteAfter($storeId = null)
    {
        return $this->getConfigSchedule('delete_queue_log', $storeId);
    }

    /**
     * @return array|mixed
     */
    public function getSchedule()
    {
        return $this->getConfigSchedule('schedule');
    }

    /**
     * @param null $storeId
     *
     * @return array|mixed
     */
    public function getLimitObjectSend($storeId = null)
    {
        return $this->getConfigSchedule('obj_send_per_time', $storeId);
    }

    /**
     * @return string
     */
    public function getLastSchedule()
    {
        $config = $this->configCollectionFactory->create()
            ->addFieldToFilter('path', self::CONFIG_MODULE_PATH . '/queue_schedule/last_schedule')
            ->getFirstItem();

        if ($config->getId()) {
            return $config->getValue();
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function saveLastSchedule()
    {
        $this->saveConfig(self::CONFIG_MODULE_PATH . '/queue_schedule/last_schedule', time());
    }

    /**
     * @param string $code
     *
     * @return mixed|string
     * @throws LocalizedException
     */
    public function getPaymentName($code)
    {
        foreach ($this->getPaymentMethods() as $key => $paymentMethod) {
            if ($code === $key) {
                return $paymentMethod;
            }
        }

        return '';
    }

    /**
     * @param null $store
     *
     * @return array
     * @throws LocalizedException
     */
    public function getPaymentMethods($store = null)
    {
        $methods = [];

        foreach ($this->helperPayment->getPaymentMethods() as $code => $data) {
            if (isset($data['active'])) {
                $storedTitle = $this->helperPayment->getMethodInstance($code)->getConfigData('title', $store);

                if (isset($storedTitle)) {
                    $methods[$code] = $this->getConfigValue(
                        'payment/' . $code . '/title',
                        $this->getWebsiteIdPaymentRule(),
                        ScopeInterface::SCOPE_WEBSITES
                    );

                    if (!isset($methods[$code])) {
                        $methods[$code] = $storedTitle;
                    }
                } elseif (isset($data['title'])) {
                    $methods[$code] = $data['title'];
                }
            }
        }

        asort($methods);

        return $methods;
    }

    /**
     * @return mixed
     */
    public function getWebsiteIdPaymentRule()
    {
        return $this->sync->getWebsiteIdPaymentRule();
    }

    /**
     * @return bool
     * @throws LocalizedException
     */
    public function haveCompany()
    {
        $companyId   = $this->getCompanyId();
        $companyInfo = $this->sendRequest(
            $this->getMode() . $companyId . '/companyinfo/' . $companyId,
            Request::METHOD_GET
        );

        return isset($companyInfo['CompanyInfo']);
    }

    /**
     * @param string $realmId
     */
    public function createCustomerGuest($realmId)
    {
        $response = $this->requestData(
            Request::METHOD_POST,
            $this->getMode() . $realmId . '/customer?minorversion=41',
            ['DisplayName' => __('Customer Guest using for sync from Magento ') . time()]
        );

        if ($response && is_array($response) && isset($response['Customer'])) {
            $this->saveConfig('customer_guest', $response['Customer']['Id']);
        }
    }

    /**
     * @return mixed
     */
    public function getCustomerGuestId()
    {
        return $this->getConfigGeneral('customer_guest');
    }
}
