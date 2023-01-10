<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Used in creating options for Yes|No|Specified config value selection
 *
 */
namespace Mageants\GoogleCustomerReviews\Model\Config\Source;

use Magento\Framework\Locale\TranslatedLists;
/**
 * @api
 * @since 100.0.2
 */
class PostionPopup implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            'CENTER_DIALOG' => __('CENTER'),
            'TOP_LEFT_DIALOG' => __('TOP LEFT'),
            'TOP_RIGHT_DIALOG' => __('TOP RIGHT'),
            'BOTTOM_LEFT_DIALOG' => __('BOTTOM LEFT'),
            'BOTTOM_RIGHT_DIALOG' => __('BOTTOM RIGHT'),
            'BOTTOM_TRAY' => __('BOTTOM TRAY')
        ];
    }
}
