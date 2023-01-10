<?php
/**
 * @category Mageants FacebookChat
 * @package Mageants_FacebookChat
 * @copyright Copyright (c) 2022 Mageants
 * @author Mageants Team <info@mageants.com>
 */
namespace Mageants\FacebookChat\Model\Config\Source;

class DisplayOnPage implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'all_page', 'label' => __('All Pages')],
            ['value' => 'cms_index_index', 'label' => __('Homepage')]
        ];
    }
}
