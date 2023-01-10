<?php
/**
 * Copyright © 2015 Mageants. All rights reserved.
 */

namespace Mageants\ImageGallery\Model;

use Mageants\ImageGallery\Api\Data\CategoryInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Category extends \Magento\Framework\Model\AbstractModel implements CategoryInterface, IdentityInterface
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
    public const CACHE_TAG = 'imagegallery_category';

    /**
     * @var string
     */
    protected $_cacheTag = 'imagegallery_category';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'imagegallery_category';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mageants\ImageGallery\Model\ResourceModel\Category::class);
    }

    /**
     * Prepare slider's statuses.
     *
     * Available event slider_get_available_statuses to customize statuses.
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
        return $this->getData(self::CATEGORY_ID);
    }

    /**
     * Get Slider Name
     *
     * @return string
     */
    public function getCategoryName()
    {
        return $this->getData(self::CATEGORY_NAME);
    }

    /**
     * Get image id
     */
    public function getImageId()
    {
        return $this->getData(self::IMAGE_ID);
    }
    
    /**
     * Get slides
     *
     * @return string|null
     */
    public function getImages()
    {
        return $this->getData(self::SLIDES);
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
     * @return \Webspeaks\BannerSlider\Api\Data\SliderInterface
     */
    public function setId($id)
    {
        return $this->setData(self::CATEGORY_ID, $id);
    }

    /**
     * Set Slider Name
     *
     * @param string $category_name
     * @return \Webspeaks\BannerSlider\Api\Data\SliderInterface
     */
    public function setCategoryName($category_name)
    {
        return $this->setData(self::CATEGORY_NAME, $category_name);
    }

    /**
     * Set slides
     *
     * @param string $slides
     * @return \Webspeaks\BannerSlider\Api\Data\SliderInterface
     */
    public function setImages($slides)
    {
        return $this->setData(self::IMAGES, $title);
    }

    /**
     * Set image id
     *
     * @param int $image_id
     */
    public function setImageId($image_id)
    {
        return $this->setData(self::IMAGE_ID, $image_id);
    }
    
    /**
     * Set is active
     *
     * @param bool $is_active
     */
    public function setIsActive($is_active)
    {
        return $this->setData(self::IS_ACTIVE, $is_active);
    }

    /**
     * Get availabe slide.
     *
     * @return []
     */
    public function getAvailableCategory()
    {

        $options = [];
        $categoryCollection = $this->getResourceCollection()
                            ->addFieldToFilter('is_active', 1);
        foreach ($categoryCollection as $category) {
            $options[] = [
                'value' => $category->getId(),
                'label' => $category->getCategoryName(),
                'level' => 1,
            ];
        }

        return $options;
    }
}
