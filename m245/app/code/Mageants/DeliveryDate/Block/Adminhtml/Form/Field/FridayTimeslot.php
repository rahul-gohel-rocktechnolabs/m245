<?php

/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\DeliveryDate\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class FridayTimeslot extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var startHours
     */
    protected $_startHourRenderer = null;
    /**
     * @var startMin
     */
    protected $_startMinRenderer = null;
    /**
     * @var endHours
     */
    protected $_endHourRenderer = null;
    /**
     * @var endMin
     */
    protected $_endMinRenderer = null;
    /**
     * @var enableRender
     */
    protected $_enableRenderer = null;

    /**
     * Get Start Hour function
     *
     * @return mixed
     */
    public function _getStartHourGroupRenderer()
    {
        if (!$this->_startHourRenderer) {
            $this->_startHourRenderer = $this->getLayout()->createBlock(
                \Mageants\DeliveryDate\Block\Adminhtml\Form\Field\TimeslotRender::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_startHourRenderer->setClass('from_select');
        }
        return $this->_startHourRenderer;
    }
    /**
     * Get Start Minutes function
     *
     * @return mixed
     */
    public function _getStartMinuteGroupRenderer()
    {
        if (!$this->_startMinRenderer) {
            $this->_startMinRenderer = $this->getLayout()->createBlock(
                \Mageants\DeliveryDate\Block\Adminhtml\Form\Field\MinuteRender::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_startMinRenderer->setClass('to_select');
        }
        return $this->_startMinRenderer;
    }
    /**
     * Get End Hours function
     *
     * @return mixed
     */
    public function _getEndHourGroupRenderer()
    {
        if (!$this->_endHourRenderer) {
            $this->_endHourRenderer = $this->getLayout()->createBlock(
                \Mageants\DeliveryDate\Block\Adminhtml\Form\Field\TimeslotRender::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_endHourRenderer->setClass('from_select');
        }
        return $this->_endHourRenderer;
    }

    /**
     * Get End Minutes func
     *
     * @return mixed
     */
    public function _getEndMinuteGroupRenderer()
    {
        if (!$this->_endMinRenderer) {
            $this->_endMinRenderer = $this->getLayout()->createBlock(
                \Mageants\DeliveryDate\Block\Adminhtml\Form\Field\MinuteRender::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_endMinRenderer->setClass('to_select');
        }
        return $this->_endMinRenderer;
    }
    /**
     * Get Enable Group function
     *
     * @return mixed
     */
    public function _getEnableGroupRenderer()
    {
        return $this->_enableRenderer;
    }
    /**
     * Get Prepare Render function
     *
     * @return void
     */
    public function _prepareToRender()
    {
        $this->addColumn('start_hour', [
            'label' => __('Start Hour'),
            'renderer' => $this->_getStartHourGroupRenderer()
        ]);
        $this->addColumn('start_min', [
            'label' => __('Start Minute'),
            'renderer' => $this->_getStartMinuteGroupRenderer()
        ]);
        $this->addColumn('end_hour', [
            'label' => __('End Hour'),
            'renderer' => $this->_getEndHourGroupRenderer()
        ]);
        $this->addColumn('end_min', [
            'label' => __('End Minute'),
            'renderer' => $this->_getEndMinuteGroupRenderer()
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add New');
    }

    /**
     * Prepare Row function
     *
     * @param \Magento\Framework\DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {

        $optionExtraAttr = [];
        $optionExtraAttr['option_' .
            $this->_getStartHourGroupRenderer()->calcOptionHash($row->getData('start_hour'))] = 'selected="selected"';
        $optionExtraAttr['option_' .
            $this->_getStartMinuteGroupRenderer()->calcOptionHash($row->getData('start_min'))] = 'selected="selected"';
        $optionExtraAttr['option_' .
            $this->_getEndHourGroupRenderer()->calcOptionHash($row->getData('end_hour'))] = 'selected="selected"';
        $optionExtraAttr['option_' .
            $this->_getEndMinuteGroupRenderer()->calcOptionHash($row->getData('end_min'))] = 'selected="selected"';
        $row->setData('option_extra_attrs', $optionExtraAttr);
    }
}
