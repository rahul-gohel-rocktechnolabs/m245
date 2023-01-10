<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Block\Order;

class PrintAttributes extends \Mageants\Orderattribute\Block\Order\Attributes
{
    /**
     * Get list
     *
     * @return object
     */
    public function getList()
    {
        $orderModel = $this->getOrder();
        $this->orderValue->loadByOrderId($orderModel->getId());

        $list = $this->orderValue->getOrderAttributeValuesForPrintHtml(
            $orderModel->getStoreId()
        );

        return $list;
    }
}
