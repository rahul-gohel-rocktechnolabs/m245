<?php

/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\DeliveryDate\Block\Adminhtml\DeliveryInformation;

use Magento\Backend\Block\Widget\Form\Container;

class Edit extends Container
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Constructor function
     *
     * @return mixed
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Mageants_DeliveryDate';
        $this->_controller = 'Adminhtml_DeliveryInformation';
        parent::_construct();
        $this->buttonList->remove('delete');
        $this->buttonList->update('save', 'label', __('Save Delivery Date'));
    }
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Getter of url for "Save and Continue" button
     *
     * @return string
     */
    public function getBackUrl()
    {
        $address = $this->_coreRegistry->registry('order_address');
        return $this->getUrl('sales/order/view', ['order_id' => $this->getRequest()->getParam('id')]);
    }

    /**
     * Save Url function
     *
     * @return string
     */
    protected function _getSaveUrl()
    {
        return $this->getUrl(
            'delivery/information/save',
            [
                'order_id' => $this->getRequest()->getParam('id'),
                '_current' => true, '_use_rewrite' => true
            ]
        );
    }
}
