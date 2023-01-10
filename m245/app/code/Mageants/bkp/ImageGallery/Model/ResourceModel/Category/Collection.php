<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageants\ImageGallery\Model\ResourceModel\Category;

/**
 * Categorys Collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var \Mageants\ImageGallery\Model\Category
     */
    protected $_idFieldName = 'category_id';
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            \Mageants\ImageGallery\Model\Category::class,
            \Mageants\ImageGallery\Model\ResourceModel\Category::class
        );
    }
}
