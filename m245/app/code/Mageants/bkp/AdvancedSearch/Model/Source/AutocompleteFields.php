<?php

/**
 * @category Mageants_AdvancedSearch
 * @package Mageants_AdvancedSearch
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\AdvancedSearch\Model\Source;

class AutocompleteFields
{
    public const SUGGEST = 'suggest';

    public const PRODUCT = 'product';

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $this->options = [
            ['value' => self::SUGGEST, 'label' => __('Suggested')],
            ['value' => self::PRODUCT, 'label' => __('Products')],
        ];

        return $this->options;
    }
}
