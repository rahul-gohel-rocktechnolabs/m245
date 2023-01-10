<?php
/**
 * @category Mageants ExtraFee
 * @package Mageants_ExtraFee
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\ExtraFee\Controller\Adminhtml\ExtraFee;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;
use \Mageants\ExtraFee\Model\ExtraFee;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var ExtraFee
     */
    protected $extrafee;

    /**
     * Constructor
     *
     * @param Action\Context $context
     * @param ExtraFee $extrafee
     */
    public function __construct(
        Action\Context $context,
        ExtraFee $extrafee
    ) {
        $this->extrafee = $extrafee;
        parent::__construct($context);
    }
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->extrafee;
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('The Item has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a item to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
