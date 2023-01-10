<?php
/**
 * @category Mageants OrderArchive
 * @package Mageants OrderArchive
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\OrderArchive\Controller\Adminhtml\Archive;

use Magento\Framework\Controller\ResultFactory;

class Add extends \Mageants\OrderArchive\Controller\Adminhtml\Archive
{

    /**
     * @var \Mageants\OrderArchiveOrderArchive\Model\Archive
     */
    protected $_archiveModel;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

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
    const ADMIN_RESOURCE = 'Mageants_OrderArchive::add';

    /**
     * Archive order action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $orderEmailParams = $objectManager->create('\Magento\Sales\Model\Order')->load($orderId);
        $customerEmail = $orderEmailParams->getCustomerEmail();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if ($orderId) {
            $this->_archiveModel->archiveOrdersById($orderId);
            $this->messageManager->addSuccess(__('We have archived the order.'));
            $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
            try {
                $senderEmail = "sender_address@example.com";
                $senderName = "Sender Name";

        
                $identifier = 3;  // Enter your email template identifier here
                $transport = $this->transportBuilder
                ->setTemplateIdentifier('email_section_sendmail_email_template')
                ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
                ->setTemplateVars([
                   'templateVar'  => $orderId,
                ])
                ->setFrom(['name' => $senderName,'email' => $senderEmail])
                ->addTo([$customerEmail])
                ->getTransport();
                $transport->sendMessage();
                $this->messageManager->addSuccess(__('Archive Email has been sent successfully.'));
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong. Please try again later.'));
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }

        } else {
            $this->messageManager->addError(__('Please specify the order ID to be archived.'));
            $resultRedirect->setPath('sales/order');
        }

        return $resultRedirect;
    }
}
