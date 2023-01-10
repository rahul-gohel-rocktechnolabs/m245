<?php
/**
 * @category Mageants AgeVerification
 * @package Mageants_AgeVerification
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <info@mageants.com>
 */
namespace Mageants\AgeVerification\Model\Config\Source;

class VerificationBasedOn implements \Magento\Framework\Option\ArrayInterface
{
   /**
    * Undocumented function
    *
    * @return void
    */
    public function toOptionArray()
    {
        return [
            ['value' => 'global', 'label' => __('Global')],
            ['value' => 'category', 'label' => __('Category')],
            ['value' => 'specific-pages', 'label' => __('Specific Pages')]
        ];
    }
}
