<?php
/**
 * Copyright © 2015 Mageants. All rights reserved.
 */
namespace Mageants\ImageGallery\Model\ResourceModel;

/**
 * Category resource
 */
class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('imagegallery_category', 'category_id');
    }
}
