<?php
/**
 * @category Mageants AgeVerification
 * @package Mageants_AgeVerification
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <info@mageants.com>
 */
namespace Mageants\AgeVerification\Controller\Adminhtml\Template;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResponseInterface;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    public $filter;
    /**
     * @var \Mageants\AgeVerification\Model\ResourceModel\PopupTemplate\CollectionFactory
     */
    public $collectionFactory;
    /**
     * @var \Mageants\AgeVerification\Model\PopupTemplateFactory
     */
    public $popupTemplateFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param \Mageants\AgeVerification\Model\ResourceModel\PopupTemplate\CollectionFactory $collectionFactory
     * @param \Mageants\AgeVerification\Model\PopupTemplateFactory $popupTemplateFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        \Mageants\AgeVerification\Model\ResourceModel\PopupTemplate\CollectionFactory $collectionFactory,
        \Mageants\AgeVerification\Model\PopupTemplateFactory $popupTemplateFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->popupTemplateFactory = $popupTemplateFactory;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $templateIds = $this->getRequest()->getParam('selected');
        foreach ($templateIds as $templateId) {
            try {
                $templateModel = $this->popupTemplateFactory->create();
                // @codingStandardsIgnoreLine
                $templateModel->load($templateId)->delete();
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        if (!empty($templateIds)) {
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) were deleted.', count($templateIds))
            );
        }
            $redirect = $this->resultFactory->create(\Magento\Backend\Model\View\Result\Redirect::class);
            $redirect->setUrl('*/*/index');

            return $redirect;
    }
}
