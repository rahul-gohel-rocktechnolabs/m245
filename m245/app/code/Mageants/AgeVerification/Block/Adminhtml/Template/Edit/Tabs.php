<?php
/**
 * @category Mageants AgeVerification
 * @package Mageants_AgeVerification
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <info@mageants.com>
 */
namespace Mageants\AgeVerification\Block\Adminhtml\Template\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{
    /**
     * Class constructor
     *
     * @return void
     */
    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        parent::_construct();
        $this->setId('template_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Template Information'));
    }
   /**
    * Undocumented function
    *
    * @return void
    */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'template_info',
            [
            'label' => __('General'),
            'title' => __('General'),
            'content' => $this->getLayout()->createBlock(
                \Mageants\AgeVerification\Block\Adminhtml\Template\Edit\Tab\Info::class
            )->toHtml(),
            'active' => true
            ]
        );
        return parent::_beforeToHtml();
    }
    // @codingStandardsIgnoreEnd
}
