<?php
/**
 * @category Mageants ImageGallery
 * @package Mageants_ImageGallery
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ImageGallery\Api\Data;

interface CategoryInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const CATEGORY_ID     = 'category_id';
    public const IMAGE_ID     = 'image_id';
    public const CATEGORY_NAME   = 'category_name';
    public const IMAGES        = 'images';
    public const IS_ACTIVE     = 'is_active';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get category Name
     *
     * @return string
     */
    public function getCategoryName();

    /**
     * Get image
     *
     * @return string|null
     */
    public function getImages();

    /**
     * Is active
     *
     * @return bool|null
     */
    public function isActive();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Mageants\ImageGallery\Api\Data\CategoryInterface
     */
    public function setId($id);

    /**
     * Set category name
     *
     * @param string $category_name
     * @return \Mageants\ImageGallery\Api\Data\CategoryInterface
     */
    public function setCategoryName($category_name);

    /**
     * Set title
     *
     * @param string $images
     * @return \Mageants\ImageGallery\Api\Data\CategoryInterface
     */
    public function setImages($images);
    /**
     * Set is active
     *
     * @param int|bool $isActive
     * @return \Mageants\ImageGallery\Api\Data\CategoryInterface
     */
    public function setIsActive($isActive);
}
