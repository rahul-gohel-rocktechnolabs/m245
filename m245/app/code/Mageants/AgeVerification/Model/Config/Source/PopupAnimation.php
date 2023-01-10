<?php
/**
 * @category Mageants AgeVerification
 * @package Mageants_AgeVerification
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <info@mageants.com>
 */
namespace Mageants\AgeVerification\Model\Config\Source;

class PopupAnimation implements \Magento\Framework\Option\ArrayInterface
{
   /**
    * Undocumented function
    *
    * @return void
    */
    public function toOptionArray()
    {
        return [
        ['value' => 'zoomIn', 'label' => __('Zoom In')],
        ['value' => 'fadeIn', 'label' => __('Fade In')],
        ['value' => 'flipInX', 'label' => __('Flip In')],
        ['value' => 'slideInDown', 'label' => __('Slide In')],
        ['value' => 'bounceIn', 'label' => __('Bounce In')]
        ];
    }
}
