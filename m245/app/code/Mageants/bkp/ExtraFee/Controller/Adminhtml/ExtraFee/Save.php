<?php
/**
 * @category Mageants ExtraFee
 * @package Mageants_ExtraFee
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\ExtraFee\Controller\Adminhtml\ExtraFee;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\TestFramework\ErrorLog\Logger;
use Magento\Backend\Helper\Js;
use \Mageants\ExtraFee\Model\ExtraFee;
use \Magento\Backend\Model\Session;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $_jsHelper;

    /**
     * @var ExtraFee
     */
    protected $extrafee;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Constructor
     *
     * @param Action\Context $context
     * @param Js $jsHelper
     * @param ExtraFee $extrafee
     * @param Session $session
     */
    public function __construct(
        Context $context,
        Js $jsHelper,
        ExtraFee $extrafee,
        Session $session
    ) {
        $this->_jsHelper = $jsHelper;
        $this->extrafee = $extrafee;
        $this->session = $session;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageants_ExtraFee::save');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
      
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            /** @var \Mageants\ExtraFee\Model\ExtraFee $model */
            $model = $this->extrafee;
            $id = $this->getRequest()->getParam('id');
            
            if (isset($data['store_id'])) {
                if (in_array('0', $data['store_id'])) {
                    $data['store_id'] = '0';
                } else {
                    $data['store_id'] = implode(",", $data['store_id']);
                }
            }
            if (isset($data['category_ids'])) {
                if (in_array('0', $data['category_ids'])) {
                    $data['category_ids'] = '0';
                } else {
                    $data['category_ids'] = implode(",", $data['category_ids']);
                }
            }
            
            if ($id) {
                $model->load($id);
            }
            $model->setData($data);
            try {
                $model->save();

                $this->messageManager->addSuccess(__('You saved this Item.'));
                $this->session->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the item.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
