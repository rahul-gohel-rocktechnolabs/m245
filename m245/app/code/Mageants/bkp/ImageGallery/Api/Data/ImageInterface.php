<?php
/**
 * @category Mageants ImageGallery
 * @package Mageants_ImageGallery
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ImageGallery\Api\Data;

interface ImageInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const IMAGE_ID      = 'image_id';
    public const IMAGE_TITLE   = 'image_title';
    public const IMAGE   = 'image';
    public const IS_ACTIVE     = 'is_active';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Image title
     *
     * @return string
     */
    public function getImageTitle();

    /**
     * Get Image
     */
    public function getImage();

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
     * @return \Mageants\ImageGallery\Api\Data\ImageInterface
     */
    public function setId($id);

    /**
     * Set Image title
     *
     * @param string $image_title
     * @return \Mageants\ImageGallery\Api\Data\ImageInterface
     */
    public function setImageTitle($image_title);

    /**
     * Set image
     *
     * @param string $image
     */
    public function setImage($image);
    /**
     * Set is active
     *
     * @param int|bool $isActive
     * @return \Mageants\ImageGallery\Api\Data\ImageInterface
     */
    public function setIsActive($isActive);
}
