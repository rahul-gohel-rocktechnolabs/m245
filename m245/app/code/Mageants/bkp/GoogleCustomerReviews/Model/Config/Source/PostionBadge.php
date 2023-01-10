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

/**
 * @api
 * @since 100.0.2
 */
class PostionBadge implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            'BOTTOM_LEFT' => __('BOTTOM LEFT'),
            'BOTTOM_RIGHT' => __('BOTTOM RIGHT'),
        ];
    }
}
