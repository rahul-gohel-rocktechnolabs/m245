<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Plugin\Order;

class PrintOrder
{
    /**
     * After to html
     *
     * @param object $subject
     * @param object $result
     * @return object
     */
    public function afterToHtml($subject, $result)
    {
        if ($subject->getNameInlayout() == 'sales.order.print.invoice' ||
            $subject->getNameInlayout() == 'sales.order.print.shipment'
        ) {
            $orderAttributesForm = $subject->getLayout()->createBlock(
                \Mageants\Orderattribute\Block\Order\PrintAttributes::class
            );
            $orderAttributesForm->setTemplate('Mageants_Orderattribute::order/view/attributes.phtml');
            $orderAttributesForm->setStore($subject->getStore());
            $orderAttributesFormHtml = $orderAttributesForm->toHtml();
            $result = $this->getStrLReplaceContent('</div>', $orderAttributesFormHtml . '</div>', $result);
        }

        return $result;
    }

    /**
     * Get string left Replace Content
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @return string
     */
    protected function getStrLReplaceContent($search, $replace, $subject)
    {
        $pos = strrpos($subject, $search);
        if ($pos !== false) {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }
        return $subject;
    }
}
