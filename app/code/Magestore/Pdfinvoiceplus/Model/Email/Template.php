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

namespace Magestore\Pdfinvoiceplus\Model\Email;

/**
 * class Template
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Template extends \Magento\Email\Model\Template
{
    protected $_hasDesignBeenApplied;

    /**
     * Apply design config so that emails are processed within the context of the appropriate area/store/theme.
     * Can be called multiple times without issue.
     *
     * @return bool
     */
    protected function applyDesignConfig()
    {
        // Only run app emulation if this is the parent template and emulation isn't already running.
        // Otherwise child will run inside parent emulation.
        if ($this->isChildTemplate() || $this->_hasDesignBeenApplied) {
            return false;
        }
        $this->_hasDesignBeenApplied = true;

        $designConfig = $this->getDesignConfig();
        $storeId = $designConfig->getStore();
        $area = $designConfig->getArea();
        if ($storeId !== null) {
            // Force emulation in case email is being sent from same store so that theme will be loaded. Helpful
            // for situations where emails may be sent from bootstrap files that load frontend store, but not theme
//            $this->appEmulation->startEnvironmentEmulation($storeId, $area, true);
        }
        return true;
    }

    /**
     * Revert design settings to previous
     *
     * @return $this
     */
    protected function cancelDesignConfig()
    {
        $this->appEmulation->stopEnvironmentEmulation();
        $this->_hasDesignBeenApplied = false;
        return $this;
    }
}