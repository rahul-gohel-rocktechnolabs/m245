<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageants\ImageGallery\Model\ResourceModel\Video;

/**
 * Gallerys Collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var \Mageants\ImageGallery\Model\Video
     */
    protected $_idFieldName = 'video_id';
    
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            \Mageants\ImageGallery\Model\Video::class,
            \Mageants\ImageGallery\Model\ResourceModel\Video::class
        );
    }
}
