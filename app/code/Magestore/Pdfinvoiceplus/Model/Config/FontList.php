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

namespace Magestore\Pdfinvoiceplus\Model\Config;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * class FontList
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class FontList implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var array
     */
    protected $_fontList = [];

    /**
     * FontList constructor.
     * @param \Magento\Framework\Filesystem $filesystem
     * @param array $data
     */
    public function __construct(\Magento\Framework\Filesystem $filesystem, array $data = [])
    {
        $this->_filesystem = $filesystem;
        $this->_construct();
    }

    /**
     *
     */
    protected function _construct()
    {
        $fontConfigPath = $this->_filesystem->getDirectoryWrite(DirectoryList::LIB_INTERNAL)
            ->getAbsolutePath('mPdf/config_fonts.php');

        require_once $fontConfigPath;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        foreach ($this->fontdata as $key => $value) {
            $this->_fontList[] = [
                'value' => $key,
                'label' => preg_replace('/\\.[^.\\s]{3,4}$/', '', $value['R'])
            ];
        }

        return $this->getFontList();
    }

    /**
     * @return array
     */
    public function getFontList()
    {
        return $this->_fontList;
    }
}