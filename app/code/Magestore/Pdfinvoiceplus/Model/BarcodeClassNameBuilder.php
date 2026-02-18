<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Pdfinvoiceplus\Model;

/**
 * class BarcodeClassNameBuilder
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class BarcodeClassNameBuilder extends \Magento\Framework\DataObject
{
    /**
     *
     */
    const DEFAULT_CLASS_NAME = 'barcode';
    /**
     *
     */
    const CLASS_NAME_HIDE_BARCODE = 'hide-barcode';

    /**
     * @var array
     */
    protected $_classNames = ['barcode'];

    /**
     * @var bool
     */
    protected $_isShow = true;

    /**
     * @var string
     */
    protected $_type = 'QR';

    /**
     * BarcodeClassNameBuilder constructor.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->_construct();
    }

    /**
     *
     */
    protected function _construct()
    {
        if ($this->hasData('is_show')) {
            $this->setIsShow($this->getData('is_show'));
        }

        if ($this->hasData('type')) {
            $this->setType($this->getData('type'));
        }
    }

    /**
     * @return string
     */
    public function buildClassNames()
    {
        if (!$this->isIsShow()) {
            $this->addClassName(self::CLASS_NAME_HIDE_BARCODE);
        } else {
            $this->removeClassName(self::CLASS_NAME_HIDE_BARCODE);
        }

        $this->addClassName('barcode-type-' . strtolower($this->getType()));

        return implode(' ', $this->_classNames);
    }

    /**
     * @param $className
     *
     * @return $this
     */
    public function addClassName($className)
    {
        if (is_string($className) && !array_key_exists($className, $this->_classNames)) {
            $this->_classNames[] = $className;
        }

        return $this;
    }

    /**
     * @param $className
     *
     * @return $this
     */
    public function removeClassName($className)
    {
        if (is_string($className) && array_key_exists($className, $this->_classNames)) {
            unset($this->_classNames[$className]);
        }

        return $this;
    }

    /**
     * @return boolean
     */
    public function isIsShow()
    {
        return $this->_isShow;
    }

    /**
     * @param string $type
     *
     * @return BarcodeClassNameBuilder
     */
    public function setType($type)
    {
        $this->_type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @param boolean $isShow
     *
     * @return BarcodeClassNameBuilder
     */
    public function setIsShow($isShow)
    {
        $this->_isShow = $isShow;

        return $this;
    }
}