<?php
/**
 * Copyright © 2015 Mageants. All rights reserved.
 */

namespace Mageants\ImageGallery\Model;

use Mageants\ImageGallery\Api\Data\VideoInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Video extends \Magento\Framework\Model\AbstractModel implements VideoInterface, IdentityInterface
{

    /**#@+
     * Slider's Statuses
     */
    public const STATUS_ENABLED = 1;
    public const STATUS_DISABLED = 0;
    /**#@-*/

    /**
     * CMS page cache tag
     */
    public const CACHE_TAG = 'imagegallery_gallery';

    /**
     * @var string
     */
    protected $_cacheTag = 'imagegallery_gallery';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'imagegallery_gallery';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mageants\ImageGallery\Model\ResourceModel\Video::class);
    }

    /**
     * Prepare slide's statuses.
     *
     * Available event slide_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::VIDEO_ID);
    }

    /**
     * Get Slide title
     *
     * @return string
     */
    public function getVideoTitle()
    {
        return $this->getData(self::VIDEO_TITLE);
    }
    
    /**
     * Get video
     */
    public function getVideo()
    {
        return $this->getData(self::VIDEO);
    }

    /**
     * Is active
     *
     * @return bool|null
     */
    public function isActive()
    {
        return (bool) $this->getData(self::IS_ACTIVE);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return \Webspeaks\BannerSlider\Api\Data\SlideInterface
     */
    public function setId($id)
    {
        return $this->setData(self::VIDEO_ID, $id);
    }

    /**
     * Set Slide title
     *
     * @param string $video_title
     * @return \Webspeaks\BannerSlider\Api\Data\SlideInterface
     */
    public function setVideoTitle($video_title)
    {
        return $this->setData(self::VIDEO_TITLE, $image_title);
    }
     /**
      * Get video
      *
      * @param string $video
     n*/
    public function setVideo($video)
    {
        return $this->setData(self::VIDEO, $image);
    }
    /**
     * Set is active
     *
     * @param int|bool $is_active
     * @return \Webspeaks\BannerSlider\Api\Data\SlideInterface
     */
    public function setIsActive($is_active)
    {
        return $this->setData(self::IS_ACTIVE, $is_active);
    }
}
