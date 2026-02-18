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

namespace Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\TotalRender;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Checkbox
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
abstract class TotalsAbstractRender implements \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderInterface
{
    /**
     * @var array
     */
    protected $_totalVarsCode = [];

    /**
     * @var
     */
    protected $_varPrefix;

    /**
     * @var
     */
    protected $_renderingEntity;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var
     */
    protected $_area;

    /**
     * @var
     */
    protected $_processedHtml;

    /**
     * @var
     */
    protected $_html;

    /**
     * @var \Magestore\Pdfinvoiceplus\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magestore\Pdfinvoiceplus\Model\Localization
     */
    protected $_localization;

    /**
     * TotalsAbstractRender constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\Pdfinvoiceplus\Helper\Data $helper
     * @param \Magestore\Pdfinvoiceplus\Model\Localization $localization
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magestore\Pdfinvoiceplus\Helper\Data $helper,
        \Magestore\Pdfinvoiceplus\Model\Localization $localization,
        array $data = []
    )
    {
        $this->_objectManager = $objectManager;
        $this->_registry = $registry;
        $this->_helper = $helper;
        $this->_localization = $localization;
        $this->_setTotalVarsCode();
        $this->_contruct();
    }

    /**
     * @return $this
     */
    protected function _contruct()
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
     * @return mixed
     */
    public function getRenderingEntity()
    {
        return $this->_renderingEntity;
    }

    /**
     * @param mixed $renderingEntity
     */
    public function setRenderingEntity($renderingEntity)
    {
        $this->_renderingEntity = $renderingEntity;
    }

    /**
     * fill total in html template
     * @param string $html
     * @return mixed
     */
    public function processTotalHtml($html = '')
    {
        if ($html != '') {
            $this->_processedHtml = $html;
        }
        $_html = $this->_processedHtml;
        if ($this->_processedHtml == '') {
            return '';
        }

        $totalHtml = $this->_getTotalHtmlVars($_html);
        $totalsVarsArray = $this->_getTotalsVarArray();

        $totals = [];
        foreach ($totalHtml as $varName => $total) {
            if (isset($totalsVarsArray[$varName])) {
                foreach ($totalsVarsArray as $key => $val) {
                    if ($key == $varName) {
                        break;
                    }
                    if (isset($totalHtml[$key])) {
                        $totalHtml[$key]['value'] = $val['value'];
                    } else {
                        $totals[$key] = $val;
                    }
                    unset($totalsVarsArray[$key]);
                }
                $totals[$varName]['value'] = $totalsVarsArray[$varName]['value'];
                $totals[$varName]['label'] = $total['label'];
                unset($totalsVarsArray[$varName]);
            } else {
                $totals[$varName] = $total;
            }
            unset($totalHtml[$varName]);
        }
        if (count($totalsVarsArray) > 0) {
            $totals = array_merge($totals, $totalsVarsArray);
        }

        $dom = new \simple_html_dom();
        $dom->load($_html);

        $total_wrap = $dom->find('.body-total', 0);
        if (!is_null($total_wrap)) {
            $childs_rows = $total_wrap->find('.total-row');
            $total_wrap->innertext = ''; //reset inner html col
        } else {
            return $this->_html;
        }
        if (is_null($childs_rows)) {
            return $this->_html;
        }
        //get idx vars html
        $idx = 0;
        $vars_idx = array();
        foreach ($childs_rows as $child) {
            if (preg_match('/\{\{.*\}\}/', $child->find('.total-value', 0)->innertext, $matched)) {
                $vars_idx[$matched[0]] = $idx;
            }
            $idx++;
        }

        $cur_idx = 0;

        // $totals = $this->_translateLocalizationTotal($totals); // localize

        foreach ($totals as $var_key => $total) {
            if (isset($vars_idx[$var_key])) {
                $total_row = $childs_rows[$vars_idx[$var_key]];
                $total_row->children(0)->innertext = $total['label'];
                $total_row->children(1)->innertext = str_replace($var_key, $total['value'], $total_row->children(1)->innertext);
                $total_wrap->innertext .= $total_row->__toString();
                $cur_idx = $vars_idx[$var_key];
            } else {
                if (count($childs_rows) <= 0) {
                    $total_row = '<div class="total-row">'
                        . '<div class="total-label color-text">' . $total['label'] . '</div>'
                        . '<div class="total-value color-text">' . $total['value'] . '</div>'
                        . '</div>';
                    $total_wrap->innertext .= $total_row;
                } else {
                    $total_row = $childs_rows[$cur_idx];
                    $total_row->children(0)->innertext = $total['label'];
                    $total_row->children(1)->innertext = $total['value'];
                    $total_wrap->innertext .= $total_row->__toString();
                }
            }
        }

        return $dom->__toString();
    }

    /**
     * get var array from html template
     * @param $html
     * @return array
     */
    protected function _getTotalHtmlVars($html)
    {
        $dom = new \simple_html_dom();
        $dom->load($html);
        // get label array
        $rows_total = $dom->find(".body-total > .total-row");
        //merger key => value
        $arr_mer = [];
        foreach ($rows_total as $rowElement) {
            preg_match('/\{\{.*\}\}/', $rowElement->find('.total-value', 0)->innertext, $matched);
            $arr_mer[$matched[0]]['value'] = $matched[0];
            $arr_mer[$matched[0]]['label'] = $rowElement->find('.total-label', 0)->innertext;
        }
        return $arr_mer;
    }

    /**
     * get total information
     * @return mixed
     */
    abstract public function getTotals();

    /**
     * @return array("{{var name}} => array('label'=>'text','value'=>'12345')")
     */
    protected function _getTotalsVarArray()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getOrder();
        $totals = $this->getTotals();

        $totalVars = [];
        foreach ($totals as $total) {
            if ($this->_area == 'adminhtml') {
                $key = (isset($this->_totalVarsCode[$total->getCode()])) ? $this->_totalVarsCode[$total->getCode()] : $total->getCode();
                $totalVars[$key] = [
                    'label' => $total->getLabel()->getText(),
                    'value' => $order->formatPriceTxt($total->getValue())
                ];
            } else {
                if (isset($this->_totalVarsCode[$total->getCode()])) {
                    $key = $this->_totalVarsCode[$total->getCode()];
                    $totalVars[$key] = [
                        'label' => $total->getLabel()->getText(),
                        'value' => $order->formatPriceTxt($total->getValue())
                    ];
                }
            }
        }

        return $totalVars;
    }

    /**
     * @return $this
     */
    protected function _setTotalVarsCode()
    {
        $varsCode = [
            'subtotal' => "subtotal",
            'shipping' => "shipping_amount",
            'discount' => "discount_amount",
            'grand_total' => "grand_total",
            'base_grandtotal' => "base_grand_total",
        ];

        if (!isset($this->_varPrefix) || is_null($this->_varPrefix)) {
            $this->_varPrefix = '';
            return $this;
        }

        foreach ($varsCode as $key => $value) {
            $this->_totalVarsCode[$key] = '{{var ' . $this->_varPrefix . '_' . $value . '}}';
        }
        return $this;
    }

    /**
     * @param $totals
     * @return mixed
     */
    protected function _translateLocalizationTotal($totals)
    {
        $this->_localization->setLocale($this->_helper->getCurrentPdfTemplate($this->getRenderingEntity()->getStoreId())->getData('localization'));

        foreach ($totals as $key => $tt) {
            $totals[$key]['label'] = $this->_localization->translate($tt['label']);
        }

        return $totals;
    }
}