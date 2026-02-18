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
 * class Localization
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Localization
{
    /**
     *
     */
    const MODULE_NAME = 'Magestore_Pdfinvoiceplus';

    /**
     *
     */
    const LOCALE_DIR = '_locale';

    /**
     * @var \Magento\Framework\TranslateInterface
     */
    protected $_translate;

    /**
     * @var array
     */
    protected $_data = [];

    /**
     * @var
     */
    protected $_csvFileReader;

    /**
     * @var \Magento\Framework\Module\Dir
     */
    protected $_moduleDir;

    /**
     * @var string
     */
    protected $_locale;

    /**
     * @var
     */
    protected $_ioFile;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadFactory
     */
    protected $_readFactory;

    /**
     * Locale constructor.
     *
     * @param \Magento\Framework\TranslateInterface $translate
     */
    public function __construct(
        \Magento\Framework\TranslateInterface $translate,
        \Magento\Framework\Module\Dir $moduleDir,
        \Magento\Framework\File\Csv $csvFileReader,
        \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory
    )
    {
        $this->_translate = $translate;
        $this->_csvFileReader = $csvFileReader;
        $this->_moduleDir = $moduleDir;
        $this->_readFactory = $readFactory;
    }

    /**
     * @param $locale
     * @return string
     */
    public function getLocaleFilePath($locale)
    {
        return $this->_moduleDir->getDir(self::MODULE_NAME)
        . DIRECTORY_SEPARATOR . self::LOCALE_DIR . DIRECTORY_SEPARATOR . $locale . '.csv';
    }

    /**
     * @return string
     */
    public function getLocaleDirectory()
    {
        return $this->_moduleDir->getDir(self::MODULE_NAME) . DIRECTORY_SEPARATOR . self::LOCALE_DIR;
    }

    /**
     * @return $this
     */
    public function loadData()
    {
        $localeFilePath = $this->getLocaleFilePath($this->getLocale());
        try {
            $data = $this->_csvFileReader->getData($localeFilePath);
            $this->_data = [];
            foreach ($data as $item) {
                if (isset($item[0]) && isset($item[1])) {
                    $this->_data[$item[0]] = $item[1];
                }
            }
        } catch (\Exception $e) {
            $this->_data = [];
        }


        return $this;
    }

    /**
     * @param $locale
     *
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->_locale = $locale;

        return $this;
    }

    /**
     * @return array
     */
    public function getListLocaleFile()
    {
        $path = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $this->getLocaleDirectory());

        $listFiles = [];
        foreach (scandir($path) as $file) {
            if (preg_match('/\.csv$/', $file)) {
                $listFiles[] = $file;
            }
        }

        return $listFiles;
    }

    /**
     * @return array
     */
    public function getListLocale()
    {
        $listLocale = [];
        foreach ($this->getListLocaleFile() as $localFile) {
            $listLocale[] = substr($localFile, 0, 5);
        }

        return $listLocale;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * @param array $data
     *
     * @return Locale
     */
    public function setData($data)
    {
        $this->_data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Translate a word
     * @param $word
     *
     * @return string
     */
    public function translate($word)
    {
        return isset($this->_data[$word]) ? $this->_data[$word] : $word;
    }
}