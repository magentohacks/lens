<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magepow.com/) 
 * @license     http://www.magepow.com/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-02-14 20:26:27
 * @@Modify Date: 2020-10-16 16:14:15
 * @@Function:
 */

namespace Magiccart\Campaignbar\Helper;

use Magiccart\Campaignbar\Model\Design\Frontend\Responsive;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $configModule;

    /**
     * @var string
     */
    protected $pageConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Catalog Design
     *
     * @var \Magento\Catalog\Model\Design
     */

    private $catalogDesign;

    private $store;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Design $catalogDesign
    )
    {
        parent::__construct($context);
        $this->pageConfig     = $pageConfig;
        $this->_storeManager  = $storeManager;
        $this->catalogDesign  = $catalogDesign;
        $this->store          = $this->_getRequest()->getParam('store', null);
        $this->configModule   = $this->getConfig(strtolower($this->_getModuleName()), $this->store);
    }

    /**
     * Get store manager
     *
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->_storeManager;
    }

    /**
     * Get store manager
     *
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    public function getUrlMedia($image=null)
    {
        return $this->getStoreManager()->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $image;
    }

    public function getConfig($cfg='', $store = null)
    {
        if($cfg) return $this->scopeConfig->getValue( $cfg, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store );
        return $this->scopeConfig;
    }

    public function getConfigModule($cfg='', $value=null)
    {
        $values = $this->configModule;
        if( !$cfg ) return $values;
        $config  = explode('/', (string) $cfg);
        $end     = count($config) - 1;
        foreach ($config as $key => $vl) {
            if( isset($values[$vl]) ){
                if( $key == $end ) {
                    $value = $values[$vl];
                }else {
                    $values = $values[$vl];
                }
            } 

        }
        return $value;
    }

    public function getConfigArraySerialized($value)
    {
        if(!is_string($value)) return $value;
        $tmp = json_decode($value, true);
        if(json_last_error() == JSON_ERROR_NONE){
            $value = $tmp;
        } else {
            $value = @unserialize($value);
        }
        return $value;
    }

    public function getCategoryOptions()
    {
        $options = [];
        $listCfg = $this->getConfigModule('grid');
        $padding = $listCfg['padding'];
        $gridMax  = 0;
        $breakpoints = $this->getResponsiveBreakpoints(); ksort($breakpoints);
        $isOnecolumn = ($this->getPageLayout() == '1column');
        if($isOnecolumn && isset($listCfg['visible_plus'])){
            $listCfg['visible'] = $listCfg['visible_plus'];
        }
        foreach ($breakpoints as $size => $screen) {
            if( $listCfg[$screen] > $gridMax ) $gridMax = $listCfg[$screen];
            $options[]= [$size-1 => $listCfg[$screen]];
        }
        if($isOnecolumn) $gridMax--;
        return ['grid-max' => $gridMax, 'padding' => $listCfg['padding'], 'responsive' => json_encode($options)];
    }

    /**
     * @return string
     */
    public function getPageLayout()
    {
        return $this->pageConfig->getPageLayout();
    }

    /**
     * @return \Magento\Catalog\Model\Design
     */
    public function getCatalogDesign()
    {
        return $this->catalogDesign;
    }
 
    /**
     * Get custom layout settings
     *
     * @param Category|Product $object
     * @return \Magento\Framework\DataObject
     */
    public function getDesignSettings($object)
    {
        return $this->getCatalogDesign()->getDesignSettings($object);
    }


}
