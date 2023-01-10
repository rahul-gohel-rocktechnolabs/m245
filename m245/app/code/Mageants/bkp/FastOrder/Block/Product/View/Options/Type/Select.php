<?php
/**
 * @category Mageants FastOrder
 * @package Mageants_FastOrder
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\FastOrder\Block\Product\View\Options\Type;

use Magento\Catalog\Block\Product\View\Options\Type\Select as CatalogSelect;
use Magento\Catalog\Model\Product\Option;
use Magento\Framework\View\Element\Html\Select as FrameworkSelect;

class Select extends CatalogSelect
{
    /**
     * Return html for control element
     *
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getValuesHtml(): string
    {
        $sortOrder = $this->getRequest()->getParam('sortOrder');
        $_option = $this->getOption();
        $configValue = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $_option->getId());
        $store = $this->getProduct()->getStore();

        $this->setSkipJsReloadPrice(1);
        // Remove inline prototype onclick and onchange events

        if ($_option->getType() == Option::OPTION_TYPE_DROP_DOWN ||
            $_option->getType() == Option::OPTION_TYPE_MULTIPLE
        ) {
            return $this->getTypeMultiple($_option, $store, $configValue, $sortOrder);
        }

        if ($_option->getType() == Option::OPTION_TYPE_RADIO ||
            $_option->getType() == Option::OPTION_TYPE_CHECKBOX
        ) {
            $selectHtml = '<ul class="options-list nested" id="mgantsoptions-' . $_option->getId() . '-list">';
            $require = $_option->getIsRequire() ? ' required' : '';
            $arraySign = '';
            switch ($_option->getType()) {
                case Option::OPTION_TYPE_RADIO:
                    $type = 'radio';
                    $class = 'radio admin__control-radio';
                    if (!$_option->getIsRequire()) {
                        $selectHtml .= '<li class="field choice admin__field admin__field-option">' .
                            '<input type="radio" id="mgantsoptions_' .
                            $_option->getId() .
                            '" class="' .
                            $class .
                            ' product-custom-option" name="mgantsoptions[' .
                            $_option->getId() .
                            ']"' .
                            ' data-selector="options[' . $_option->getId() . ']"' .
                            ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice()"') .
                            ' value="" checked="checked" />
                            <input type="hidden"
                             name="mgantsfastorder-options['.$sortOrder.']['.$_option->getId().']" 
                             class="mgantsproduct-custom-option-select" value="" />
                            <label class="label admin__field-label" for="mgantsoptions_' .
                            $_option->getId() .
                            '"><span>' .
                            __('None') . '</span></label></li>';
                    };
                    $name = 'mgantsfastorder-options['.$sortOrder.']['.$_option->getId().']';
                    break;
                case Option::OPTION_TYPE_CHECKBOX:
                    $type = 'checkbox';
                    $class = 'checkbox admin__control-checkbox';
                    $arraySign = '[]';
                    $name = 'mgantsfastorder-options['.$sortOrder.']['.$_option->getId().'][]';
                    break;
            }
            $count = 1;
            foreach ($_option->getValues() as $_value) {
                $count++;

                $priceStr = $this->_formatPrice(
                    [
                        'is_percent' => $_value->getPriceType() == 'percent',
                        'pricing_value' => $_value->getPrice($_value->getPriceType() == 'percent'),
                    ]
                );

                $htmlValue = $_value->getOptionTypeId();
                if ($arraySign) {
                    $checked = is_array($configValue) && in_array($htmlValue, $configValue) ? 'checked' : '';
                } else {
                    $checked = $configValue == $htmlValue ? 'checked' : '';
                }

                $dataSelector = 'options[' . $_option->getId() . ']';
                if ($arraySign) {
                    $dataSelector .= '[' . $htmlValue . ']';
                }
                $selectHtml .= '<li class="field choice admin__field admin__field-option' .
                    $require .
                    '">' .
                    '<input type="' .
                    $type .
                    '" class="' .
                    $class .
                    ' ' .
                    $require .
                    ' product-custom-option"' .
                    ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice()"') .
                    ' name="mgantsoptions[' .
                    $_option->getId() .
                    ']' .
                    $arraySign .
                    '" id="mgantsoptions_' .
                    $_option->getId() .
                    '_' .
                    $count .
                    '" value="' .
                    $htmlValue .
                    '" ' .
                    $checked .
                    ' data-selector="' . $dataSelector . '"' .
                    ' price="' .
                    $this->pricingHelper->currencyByStore($_value->getPrice(true), $store, false) .
                    '" />' .
                    '<input type="hidden"
                     name="'.$name.'" 
                     class="mgantsproduct-custom-option-select" 
                     value="'.$htmlValue.'" />
                    <label class="label admin__field-label" for="mgantsoptions_' .
                    $_option->getId() .
                    '_' .
                    $count .
                    '"><span>' .
                    $_value->getTitle() .
                    '</span> ' .
                    $priceStr .
                    '</label>';
                $selectHtml .= '</li>';
            }
            $selectHtml .= '</ul>';

            return $selectHtml;
        }
    }

    /**
     * Get Type Multiple
     *
     * @param  int|null $_option
     * @param  int|null $store
     * @param  int|null $configValue
     * @param  int|null $sortOrder
     * @return string|int
     */

    protected function getTypeMultiple($_option = null, $store = null, $configValue = null, $sortOrder = null)
    {
        $require = $_option->getIsRequire() ? ' required' : '';
        $extraParams = '';
        $select = $this->getLayout()->createBlock(
            FrameworkSelect::class
        )->setData(
            [
                'id' => 'mgantsselect_' . $_option->getId(),
                'class' => $require . ' product-custom-option admin__control-select'
            ]
        );
        if ($_option->getType() == Option::OPTION_TYPE_DROP_DOWN) {
            $select->setName('mgantsoptions[' . $_option->getid() . ']')->addOption('', __('-- Please Select --'));
        } else {
            $select->setName('mgantsoptions[' . $_option->getid() . '][]');
            $select->setClass('multiselect admin__control-multiselect' . $require . ' product-custom-option');
        }
        foreach ($_option->getValues() as $_value) {
            $priceStr = $this->_formatPrice(
                [
                    'is_percent' => $_value->getPriceType() == 'percent',
                    'pricing_value' => $_value->getPrice($_value->getPriceType() == 'percent'),
                ],
                false
            );
            $select->addOption(
                $_value->getOptionTypeId(),
                $_value->getTitle() . ' ' . strip_tags($priceStr) . '',
                ['price' => $this->pricingHelper->currencyByStore($_value->getPrice(true), $store, false)]
            );
        }
        if ($_option->getType() == Option::OPTION_TYPE_MULTIPLE) {
            $extraParams = ' multiple="multiple"';
        }
        if (!$this->getSkipJsReloadPrice()) {
            $extraParams .= ' onchange="opConfig.reloadPrice()"';
        }
        $extraParams .= ' data-selector="' . $select->getName() . '"';
        $select->setExtraParams($extraParams);

        if ($configValue) {
            $select->setValue($configValue);
        }
        $clone = '<input type="hidden" 
        class="mgantsproduct-custom-option-select" 
        name="mgantsfastorder-options['.$sortOrder.']['.$_option->getid().']" 
        value=""/>';
        return $select->getHtml() . $clone;
    }
}
