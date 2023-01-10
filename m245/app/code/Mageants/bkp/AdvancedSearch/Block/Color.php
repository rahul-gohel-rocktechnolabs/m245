<?php
/**
 * @category Mageants_AdvancedSearch
 * @package Mageants_AdvancedSearch
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\AdvancedSearch\Block;

class Color extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Get color
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return void
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = $element->getElementHtml();
        $value = $element->getData('value');

        $html .= '<script type="text/javascript">
            require(["jquery","jquery/colorpicker/js/colorpicker"], function ($) {
                $(document).ready(function () {
                    var $elementId = $("#' . $element->getHtmlId() . '");
                    $elementId.css("backgroundColor", "'. $value .'");

                    $elementId.ColorPicker({
                        color: "'. $value .'",
                        onChange: function (hsb, hex, rgb) {
                            $elementId.css("backgroundColor", "#" + hex).val("#" + hex);
                        }
                    });
                });
            });
            </script>';
        return $html;
    }
}
