<?php
/**
 * Copyright © 2015 Mageants. All rights reserved.
 */

namespace Mageants\ImageGallery\Model;

use Mageants\ImageGallery\Api\Data\ImageInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Gallery extends \Magento\Framework\Model\AbstractModel implements ImageInterface, IdentityInterface
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
        $this->_init(\Mageants\ImageGallery\Model\ResourceModel\Gallery::class);
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
        return $this->getData(self::IMAGE_ID);
    }

    /**
     * Get Slide title
     *
     * @return string
     */
    public function getImageTitle()
    {
        return $this->getData(self::IMAGE_TITLE);
    }
    
    /**
     * Get Image
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
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
        return $this->setData(self::IMAGE_ID, $id);
    }

    /**
     * Set Slide title
     *
     * @param string $image_title
     * @return \Webspeaks\BannerSlider\Api\Data\SlideInterface
     */
    public function setImageTitle($image_title)
    {
        return $this->setData(self::IMAGE_TITLE, $image_title);
    }
    
    /**
     * Set image
     *
     * @param string $image
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
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
