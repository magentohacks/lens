<?php

/**
 * @Author: Alex Dong
 * @Date:   2020-07-09 16:45:27
 * @Last Modified by:   Alex Dong
 * @Last Modified time: 2020-07-09 16:46:08
 */

namespace Magepow\RecentlyViewed\Block\Widget;

use Magepow\RecentlyViewed\Model\Design\Frontend\Responsive;

class RecentlyViewed extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected $_storeManager;

	protected $jsonHelper;

	protected $registry;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        if($this->getData('slide')){
            $data['vertical-Swiping'] = $this->getData('vertical');
            $breakpoints = $this->getResponsiveBreakpoints();
            $responsive = '[';
            $num = count($breakpoints);
            foreach ($breakpoints as $size => $opt) {
                $item = (int)  $this->getData($opt);
                $responsive .= '{"breakpoint": "'.$size.'", "settings": {"slidesToShow": "'.$item.'"}}';
                $num--;
                if($num) $responsive .= ', ';
            }
            $responsive .= ']';
            $data['slides-To-Show'] = $this->getData('visible');
            $data['swipe-To-Slide'] = 'true';
            $data['responsive'] = $responsive;
            /* Fix config widget not support autoplay-Speed */
            $autoplaySpeed = $this->getData('autoplay_Speed');
            if($autoplaySpeed) {
                $data['autoplay-Speed'] = $autoplaySpeed;
            }
            
            $this->addData($data);
        }

        parent::_construct();
    }

    public function getProduct()
    {
        return $this->registry->registry('product');
    }

    public function getAjaxCfg()
    {
        $ajax = [];
        foreach (['cart', 'compare', 'wishlist', 'review', 'limit'] as $option) {
            $ajax[$option] = $this->getData($option);
        }
        return $this->jsonHelper->jsonEncode($ajax);
    }

    public function getResponsiveBreakpoints()
    {
        return Responsive::getBreakpoints();
    }

    public function getSlideOptions()
    {
        return ['autoplay', 'arrows', 'autoplay-Speed', 'dots', 'infinite', 'padding', 'vertical', 'vertical-Swiping', 'responsive', 'rows', 'slides-To-Show', 'swipe-To-Slide'];
    }

    public function getFrontendCfg()
    { 
        if($this->getSlide()) return $this->getSlideOptions();

        $this->addData(['responsive' =>json_encode($this->getGridOptions())]);

        return ['padding', 'responsive'];
    }

    public function getGridOptions()
    {
        $options = [];
        $breakpoints = $this->getResponsiveBreakpoints(); ksort($breakpoints);
        foreach ($breakpoints as $size => $screen) {
            $item = (int) $this->getData($screen);
            if(!$item) continue;
            $options[]= [$size-1 => $this->getData($screen)];
        }

        return $options;
    }

}
