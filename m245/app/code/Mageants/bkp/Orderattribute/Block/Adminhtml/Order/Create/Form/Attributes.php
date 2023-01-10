<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Block\Adminhtml\Order\Create\Form;

use \Mageants\Orderattribute\Block\Adminhtml\Order\View\Attributes\Edit\Form;

class Attributes extends Form
{

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        $this->setFieldNameSuffix('order[attributes]');
        return parent::_prepareForm();
    }

    /**
     * @inheritdoc
     */
    protected function createAttributesFieldSet($form)
    {
        $fieldset = $form->addFieldset('base_fieldset', []);
        return $fieldset;
    }

    /**
     * Get order eav attributes
     *
     * @return \Mageants\Orderattribute\Model\ResourceModel\Order\Attribute\Collection
     */
    protected function getOrderEavAttributes()
    {
        $attributesCollection = $this->attributeMetadataDataProvider
            ->loadAttributesForCreateOrderFormByStoreId($this->getStoreId());

        $groupId = $this->getCurrentGroupId();
        if (isset($groupId)) {
            $attributesCollection->addCustomerGroupFilter($groupId);
        }

        return $attributesCollection;
    }

    /**
     * Get store id
     *
     * @return int
     */
    protected function getStoreId()
    {
        if ($this->getStore()) {
            $storeId = $this->getStore()->getId();
        } else {
            $storeId = $this->_storeManager->getDefaultStoreView()->getId();
        }

        return $storeId;
    }

    /**
     * @inheritdoc
     */
    public function toHtml()
    {
        $this->_beforeToHtml();
        return parent::toHtml();
    }

    /**
     * Get current group id
     *
     * @return null|int
     */
    private function getCurrentGroupId()
    {
        $groupId = null;

        if (isset($this->backendSession->getData()['customer_data']['account']['group_id'])) {
            $groupId = $this->backendSession->getData()['customer_data']['account']['group_id'];

            if (isset($this->_request->getParams()['customer_id'])
                && $this->_request->getParams()['customer_id'] == 'false'
            ) {
                $groupId = null;
            }
        }

        if (isset($this->_request->getParams()['order']['account']['group_id'])) {
            $groupId = $this->_request->getParams()['order']['account']['group_id'];
        }

        return $groupId;
    }
}
