<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Plugin\Order\Adminhtml\Attributes;

class Form
{

    /**
     * After to html
     *
     * @param \Magento\Sales\Block\Adminhtml\Order\Create\Form\Account $subject
     * @param string $result
     * @return string
     */
    public function afterToHtml(
        \Magento\Sales\Block\Adminhtml\Order\Create\Form\Account $subject,
        $result
    ) {
        $orderAttributesForm = $subject->getLayout()->createBlock(
            \Mageants\Orderattribute\Block\Adminhtml\Order\Create\Form\Attributes::classs
        );
        $orderAttributesForm->setTemplate('Mageants_Orderattribute::order/create/attributes_form.phtml');
        $orderAttributesForm->setStore($subject->getStore());
        $orderAttributesFormHtml = $orderAttributesForm->toHtml();

        return $result . $orderAttributesFormHtml;
    }
}
