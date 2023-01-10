<?php
/**
 * Copyright © 2015 Mageants. All rights reserved.
 */
namespace Mageants\ImageGallery\Model\ResourceModel;

/**
 * Gallery resource
 */
class Gallery extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('imagegallery_gallery', 'image_id');
    }
}
