<?php

/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\DeliveryDate\Block\Adminhtml\Form\Field;

class HolidaysCalendarRender extends \Magento\Framework\View\Element\Html\Date
{
    /**
     * Html Render function
     *
     * @return void
     */
    protected function _toHtml()
    {
        $html = '<input type="text" name="' . $this->getInputName() . '" id="' . $this->getInputId() . '" ';
        $html .= 'value="<%- ' . $this->getColumnName() . ' %>" ';
        $html .= 'class="' . $this->getClass() . '" ' . $this->getExtraParams() . '/> ';
        $calendarYearsRange = $this->getYearsRange();
        $changeMonth        = $this->getChangeMonth();
        $changeYear         = $this->getChangeYear();
        $maxDate            = $this->getMaxDate();
        $showOn             = $this->getShowOn();

        $html .= '<script type="text/javascript">require(["jquery", "mage/calendar"], function($){ $("#' .
            $this->getInputId() .
            '").calendar({showsTime: ' .
            ($this->getTimeFormat() ? 'true' : 'false') .
            ',' .
            ($this->getTimeFormat() ? 'timeFormat: "' .
                $this->getTimeFormat() .
                '",' : '') .
            'dateFormat: "' .
            $this->getDateFormat() .
            '",buttonImage: "' .
            $this->getImage() .
            '",' .
            ($calendarYearsRange ? 'yearRange: "' .
                $calendarYearsRange .
                '",' : '') .
            'buttonText: "' .
            (string) new \Magento\Framework\Phrase(
                'Select Date'
            ) .
            '"' . ($maxDate ? ', maxDate: "' . $maxDate . '"' : '') .
            ($changeMonth === null ? '' : ', changeMonth: ' . $changeMonth) .
            ($changeYear === null ? '' : ', changeYear: ' . $changeYear) .
            ($showOn ? ', showOn: "' . $showOn . '"' : '') .
            '})});</script>';

        return $html;
    }
}
