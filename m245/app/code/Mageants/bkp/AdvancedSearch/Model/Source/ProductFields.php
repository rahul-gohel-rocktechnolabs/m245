<?php
/**
 * @category Mageants_AdvancedSearch
 * @package Mageants_AdvancedSearch
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\AdvancedSearch\Model\Source;

class ProductFields
{
    public const NAME = 'name';

    public const SKU = 'sku';

    public const IMAGE = 'image';

    public const REVIEWS_RATING = 'reviews_rating';

    public const SHORT_DESCRIPTION = 'short_description';

    public const DESCRIPTION = 'description';

    public const PRICE = 'price';

    public const ADD_TO_CART = 'add_to_cart';

    public const URL = 'url';

    public const CATEGORY = 'category';
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $this->options = [
            ['value' => self::NAME, 'label' => __('Product Name')],
            ['value' => self::SKU, 'label' => __('SKU')],
            ['value' => self::IMAGE, 'label' => __('Product Image')],
            ['value' => self::REVIEWS_RATING, 'label' => __('Reviews Rating')],
            ['value' => self::SHORT_DESCRIPTION, 'label' => __('Short Description')],
            ['value' => self::DESCRIPTION, 'label' => __('Description')],
            ['value' => self::PRICE, 'label' => __('Price')],
            ['value' => self::ADD_TO_CART, 'label' => __('Add to Cart Button')],
            ['value' => self::CATEGORY, 'label' => __('Category')],
        ];

        return $this->options;
    }
}
