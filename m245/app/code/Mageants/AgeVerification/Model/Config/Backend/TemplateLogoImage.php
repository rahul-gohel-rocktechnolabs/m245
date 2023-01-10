<?php
/**
 * @category Mageants AgeVerification
 * @package Mageants_AgeVerification
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <info@mageants.com>
 */

namespace Mageants\AgeVerification\Model\Config\Backend;

class TemplateLogoImage extends \Magento\Config\Model\Config\Backend\Image
{
    public const UPLOAD_DIR = 'templatelogo';

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function _getUploadDir()
    {
        return $this->_mediaDirectory->getAbsolutePath($this->_appendScopeInfo(self::UPLOAD_DIR));
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function _addWhetherScopeInfo()
    {
        return true;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function _getAllowedExtensions()
    {
        return ['jpg', 'jpeg', 'gif', 'png', 'svg'];
    }
}
