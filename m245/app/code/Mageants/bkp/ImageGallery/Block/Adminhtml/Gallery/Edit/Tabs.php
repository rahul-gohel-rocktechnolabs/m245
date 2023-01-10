<?php
/**
 * @category Mageants ImageGallery
 * @package Mageants_ImageGallery
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ImageGallery\Block\Adminhtml\Gallery\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Construct
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('checkmodule_gallery_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Gallery Information'));
    }
}
