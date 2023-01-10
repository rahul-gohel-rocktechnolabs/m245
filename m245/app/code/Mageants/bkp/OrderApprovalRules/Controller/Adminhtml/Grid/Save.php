<?php
/**
 * @category Mageants OrderApprovalRules
 * @package Mageants_OrderApprovalRules
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants <support@mageants.com>
 */

namespace Mageants\OrderApprovalRules\Controller\Adminhtml\Grid;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\TestFramework\ErrorLog\Logger;
use Magento\Framework\Serialize\SerializerInterface;
use Mageants\OrderApprovalRules\Model\OrderApprovalRules;
use Magento\Backend\Model\Session;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $_jsHelper;
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var OrderApprovalRules
     */
    protected $orderApproval;

    /**
     * @var Session
     */
    protected $backendSession;

    /**
     * @param Context $context
     * @param \Magento\Backend\Helper\Js $jsHelper
     * @param SerializerInterface $serializer
     * @param OrderApprovalRules $orderApproval
     * @param Session $backendSession
     */
    public function __construct(
        Context $context,
        \Magento\Backend\Helper\Js $jsHelper,
        SerializerInterface $serializer,
        OrderApprovalRules $orderApproval,
        Session $backendSession
    ) {
        $this->_jsHelper = $jsHelper;
        $this->serializer = $serializer;
        $this->orderApproval = $orderApproval;
        $this->backendSession = $backendSession;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageants_OrderApprovalRules::save');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            /** @var \Mageants\OrderApprovalRules\Model\OrderApprovalRules $model */
            //$model = $this->_objectManager->create('Mageants\OrderApprovalRules\Model\OrderApprovalRules');
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $this->orderApproval->load($id);
                $setData = $this->orderApproval->getData();
            } else {
                $setData = [];
            }
            $setData['rule_name'] = $data['rule_name'];
            $setData['orderstatus'] = $data['orderstatus'];
            $setData['apply_to'] = $data['apply_to'];
            if ($setData['apply_to'] == 'whole_category') {
                $setData['category_ids'] = implode(',', $data['category_ids']);
            } elseif ($setData['apply_to'] == 'specific_products') {
                if (isset($data['productids'])) {
                    $setData['product_ids'] = $data['productids'];
                }
            } elseif ($setData['apply_to'] == 'specific_users_country') {
                $setData['country_ids'] = implode(',', $data['country_ids']);
            }
            $this->orderApproval->setData($setData);
            try {
                $this->orderApproval->save();

                $this->messageManager->addSuccess(__('You saved this Item.'));
                $this->backendSession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['id' => $this->orderApproval->getId(),
                        '_current' => true]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __('Something went wrong while saving the item.'.$e->getMessage())
                );
            }

            $this->backendSession->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
