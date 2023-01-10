<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Controller\Adminhtml\Order\Attributes;

class Validate extends \Magento\Sales\Controller\Adminhtml\Order
{

    /**
     * Attributes validation action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $response = $this->_objectManager->create(\Magento\Framework\DataObject::class);
        $response->setError(false);
        return $this->resultJsonFactory->create()->setJsonData($response->toJson());
    }
}
