<?php
namespace Lens\Manager\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Model\Exception;
use Magento\Store\Model\ScopeInterface;
use Lens\Manager\Model\OptionData;
use Lens\Manager\Model\LensPrescriptionsFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends AbstractHelper
{
    /**
     * variable to check if extension is enable or not
     *
     * @var bool
     */
    const ENABLED = 'base/general/enabled';

    /**
     * variable to get licence key
     *
     * @var string
     */
    const LICENSE_KEY = 'base/general/license_key';

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    protected $lensPrescriptionsFactory;

    protected $optionData;
    /**
     * @param Context $context
     * @param CoreHelper $coreHelper
     */
    public function __construct(
        LensPrescriptionsFactory $lensPrescriptionsFactory,
        OptionData $optionData,
        Context $context
    ) {
        $this->lensPrescriptionsFactory = $lensPrescriptionsFactory;
        $this->optionData = $optionData;
        $this->_scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
    }

    /**
     * returns whether module is enabled or not
     * @param int $storeId
     * @return boolean
     */
    public function isEnabled($storeId = null)
    {
        return $this->getValue(self::ENABLED);
    }

    /**
     * Returns license key administration configuration option
     * @param int $storeId
     * @return string
     */
    public function getLicenseKey($storeId = null)
    {
        return $this->getValue(self::LICENSE_KEY,ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Helper method for retrieve config value by path and scope
     *
     * @param string $path The path through the tree of configuration values, e.g., 'general/store_information/name'
     * @param string $scopeType The scope to use to determine config value, e.g., 'store' or 'default'
     * @param null|string $scopeCode
     * @return mixed
     */
    protected function getValue($path, $scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->_scopeConfig->getValue($path, $scopeType, $scopeCode);
    }

    /**
     * get Power values from Configuration
     */
    public function getPowerValue()
    {
        return $this->getValue('manager/settings/power');
    }

    /**
     * get Cylinder values from Configuration
     */
    public function getCylinderValue()
    {
        return $this->getValue('manager/settings/cylinder');
    }

    /**
     * get Axis values from Configuration
     */
    public function getAxisValue()
    {
        return $this->getValue('manager/settings/axis');
    }

    /**
     * get Addition values from Configuration
     */
    public function getAdditionValue()
    {
        return $this->getValue('manager/settings/addition');
    }

    /**
     * get Dominance values from Configuration
     */
    public function getDominanceValue()
    {
        return $this->getValue('manager/settings/dominance');
    }

    /**
     * get Basecurve values from Configuration
     */
    public function getBasecurveValue()
    {
        return $this->getValue('manager/settings/basecurve');
    }

    /**
     * get Diamter values from Configuration
     */
    public function getDiameterValue()
    {
        return $this->getValue('manager/settings/diameter');
    }

    /**
     * Function to get Options data
     * 
     * @return array
     */
    public function getOptionsData($productId, $options)
    {
        return $this->optionData->getOptionData($productId, $options);
    }

    /**
     * function to get inventory from options
     * 
     * @param string $productId
     * @param array  $options
     */
    public function getInventoryDetails($productId, $options)
    {
        foreach ($options as $option) {
            $lens = $this->lensPrescriptionsFactory->create()->getCollection();
            foreach ($option as $param => $value) {
                $lens->addFieldToFilter(str_replace("-", "_", $param), ['eq' => $value]);
            }
            if ($lens->getFirstItem()['quantity'] > 0) {
                continue;
            } else {
                return false;
            }
        }
        return true;
    }
}