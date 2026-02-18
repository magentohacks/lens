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

namespace Magestore\Pdfinvoiceplus\Model\Variables;

/**
 * class AbstractMainVariables
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
abstract class AbstractMainVariables implements MainVariablesInterface
{
    /**
     * @var array
     */
    protected $_mainVariables = [];

    /**
     * @var
     */
    protected $_optionManager;

    /**
     * AbstractMainVariables constructor.
     *
     * @param \Magestore\Pdfinvoiceplus\Model\OptionManager $optionManager
     */
    public function __construct(\Magestore\Pdfinvoiceplus\Model\OptionManager $optionManager)
    {
        $this->_optionManager = $optionManager;
    }

    /**
     * Get prefix variable
     *
     * @return string
     */
    abstract public function getPrefixVariable();

    /**
     * get config variables option
     *
     * @return array
     */
    abstract public function getConfigVariables();

    /**
     * Show only variables is main
     *
     * @param        $arrayMain
     * @param        $arrayData
     * @param string $labelMain
     * @param string $labelMore
     *
     * @return array
     */
    protected function _maskVariables($arrayMain, $arrayData, $labelMain = 'main', $labelMore = 'more')
    {
        $more = array();
        $main = array();

        if (empty($arrayMain) && count($arrayMain) <= 0) {
            return array($labelMain => $arrayData, $labelMore => array());
        }

        foreach ($arrayData as $item) {
            if (in_array($item['value'], $arrayMain)) {
                $main[] = $item;
            } else {
                $more[] = $item;
            }
        }

        $data = array($labelMain => $main, $labelMore => $more);

        return $data;
    }

    /**
     * @return array
     */
    public function getMainVariables()
    {
        $boundVariables = [];

        foreach ($this->_mainVariables as $mainVariable) {
            $boundVariables[] = "{{var {$this->getPrefixVariable()}_{$mainVariable}}}";
        }

        return $boundVariables;
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->_maskVariables($this->getMainVariables(), $this->getConfigVariables());
    }
}