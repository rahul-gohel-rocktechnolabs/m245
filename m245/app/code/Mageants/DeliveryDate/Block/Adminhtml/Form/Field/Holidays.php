<?php

/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\DeliveryDate\Block\Adminhtml\Form\Field;

class Holidays extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var dateRender
     */
    public $_dateRenderer;

    /**
     * Get Date Column function
     *
     * @return void
     */
    public function _getDateColumnRenderer()
    {
        if (!$this->_dateRenderer) {
            $this->_dateRenderer = $this->getLayout()->createBlock(
                \Mageants\DeliveryDate\Block\Adminhtml\Form\Field\HolidaysCalendarRender::class,
                '',
                [
                    'data' => [
                        'is_render_to_js_template' => true,
                        'date_format'              => 'mm/dd/Y'
                    ]
                ]
            );
        }
        
        return $this->_dateRenderer;
    }

    /**
     * Get Prepare To Render function
     *
     * @return void
     */
    public function _prepareToRender()
    {
        $this->addColumn('Date', [ 'label' => __('Date'),'renderer' => $this->_getDateColumnRenderer()]);
        $this->addColumn('Content', [ 'label' => __('Content'), 'class'=>'required-entry', 'renderer' => false]);
        $this->_addAfter       = false;
        $this->_addButtonLabel = __('Add');
    }
}
