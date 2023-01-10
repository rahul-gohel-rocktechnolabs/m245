<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Block\Adminhtml\Order\Massaction;

class Buttons extends \Magento\Backend\Block\Widget
{
    /**
     * Prepare layout
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->getToolbar()->addChild(
            'back_button',
            \Magento\Backend\Block\Widget\Button::class,
            [
                'label' => __('Back'),
                'onclick' => 'setLocation(\'' . $this->getUrl(
                    'sales/order/view',
                    []
                ) . '\')',
                'class' => 'back'
            ]
        );

        $this->getToolbar()->addChild(
            'reset_button',
            \Magento\Backend\Block\Widget\Button::class,
            [
                'label' => __('Reset'),
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/*', ['_current' => true]) . '\')',
                'class' => 'reset',
            ]
        );

        $this->getToolbar()->addChild(
            'save_button',
            \Magento\Backend\Block\Widget\Button::class,
            [
                'label' => __('Save'),
                'class' => 'save primary',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'save', 'target' => '#attributes-edit-form']],
                ],
            ]
        );
    }
}
