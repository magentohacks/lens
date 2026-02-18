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

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * class SimpleHtmlDomFactory
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class SimpleHtmlDomFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_argumentDefault = [
        'str' => null,
        'lowercase' => true,
        'forceTagsClosed' => true,
        'target_charset' => 'UTF-8',
        'stripRN' => true,
        'defaultBRText' => "\r\n",
        'defaultSpanText' => " ",
    ];

    /**
     * SimpleHtmlDom constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
        $this->_construct();
    }

    /**
     * @return $this
     */
    protected function _construct()
    {
        if (!class_exists('simple_html_dom', false)) {
            /** @var \Magento\Framework\Filesystem $filesystem */
            $filesystem = $this->_objectManager->get('Magento\Framework\Filesystem');
            $classPath = $filesystem->getDirectoryWrite(DirectoryList::LIB_INTERNAL)
                ->getAbsolutePath('simpleHtmlDom/simple_html_dom.php');

            require_once $classPath;
        }

        return $this;
    }

    /**
     * @param array $data
     *
     * @return \simple_html_dom
     */
    public function create(array $data = [])
    {
        foreach ($this->_argumentDefault as $key => $value) {
            if (!isset($data[$key])) {
                $data[$key] = $value;
            }
        }

        return new \simple_html_dom(
            $data['str'],
            $data['lowercase'],
            $data['forceTagsClosed'],
            $data['target_charset'],
            $data['stripRN'],
            $data['defaultBRText'],
            $data['defaultSpanText']
        );
    }
}