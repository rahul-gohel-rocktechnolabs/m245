<?php
/**
 * @category Mageants OrderArchive
 * @package Mageants OrderArchive
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\OrderArchive\Controller\Adminhtml\Archive;

use Magento\Framework\Controller\ResultFactory;

class Remove extends \Mageants\OrderArchive\Controller\Adminhtml\Archive
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mageants\OrderArchive\Model\Archive $archiveModel,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_archiveModel = $archiveModel;
        $this->_fileFactory = $fileFactory;
        $this->transportBuilder = $transportBuilder;
        parent::__construct($context, $archiveModel, $fileFactory);
    }
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mageants_OrderArchive::remove';

    /**
     * Unarchive order action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $collectionsk = $this->getRequest()->getParams();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $orderEmailParams = $objectManager->create('\Magento\Sales\Model\Order')->load($orderId);
        $customerEmail = $orderEmailParams->getCustomerEmail();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($orderId) {
            $this->_archiveModel->removeOrdersFromArchiveById($orderId);
            $this->messageManager->addSuccess(__('We have removed the order from the archive.'));
            $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);

            try {
                $senderEmail = "sender_address@example.com";
                $senderName = "Sender Name";

        
                $identifier = 2;  // Enter your email template identifier here
                $transport = $this->transportBuilder
                ->setTemplateIdentifier('email_section_unarchive')
                ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
                ->setTemplateVars([
                   'templateVar'  => $orderId,
                ])
            
                ->setFrom(['name' => $senderName,'email' => $senderEmail])
                ->addTo([$customerEmail])
                ->getTransport();
                $transport->sendMessage();
                $this->messageManager->addSuccess(__('UnArchive Email has been sent successfully.'));
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong. Please try again later.'));
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }
        } else {
            $this->messageManager->addError(__('Please specify the order ID to be removed from archive.'));
            $resultRedirect->setPath('sales/order');
        }

        return $resultRedirect;
    }
}
