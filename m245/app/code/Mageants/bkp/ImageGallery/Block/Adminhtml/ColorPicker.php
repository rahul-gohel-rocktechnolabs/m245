<?php
/**
 * @category Mageants ImageGallery
 * @package Mageants_ImageGallery
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <info@mageants.com>
 */

namespace Mageants\ImageGallery\Block\Adminhtml;

class ColorPicker extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Get html element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return html
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = $element->getElementHtml();
        $value = $element->getData('value');

        $html .= '<script type="text/javascript">
        require(["jquery","jquery/colorpicker/js/colorpicker"], function ($) {


            $(document).ready(function () {
                var $el = $("#' . $element->getHtmlId() . '");
                $el.css("backgroundColor", "'. $value .'");

                // Attach the color picker
                $el.ColorPicker({
                    color: "'. $value .'",
                    onChange: function (hsb, hex, rgb) {
                        $el.css("backgroundColor", "#" + hex).val("#" + hex);
                    }
                });
            });
        });
        </script>';
        return $html;
    }
}
