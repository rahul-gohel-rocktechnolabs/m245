<?php
/**
 * @category Mageants OrderArchive
 * @package Mageants OrderArchive
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\OrderArchive\Controller\Adminhtml\Archive;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Zend\Log\Writer\Stream;
use Zend\Log\Logger;

/**
 * Class MassAdd
 */
class MassAdd extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mageants_OrderArchive::add';

    /**
     * @var \Mageants\OrderArchive\Model\Archive
     */
    protected $_archiveModel;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param Filter $filter
     * @param \Mageants\OrderArchive\Model\Archive $archiveModel
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Filter $filter,
        \Mageants\OrderArchive\Model\Archive $archiveModel,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->_archiveModel = $archiveModel;
        $this->transportBuilder = $transportBuilder;
        parent::__construct($context, $filter);
    }

    /**
     * Add selected orders to archive
     *
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $archivedIds = $this->_archiveModel->archiveOrdersById($collection->getAllIds());
        $archivedCount = count($archivedIds);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $orderIds = implode(', ', $collection->getAllIds());

        foreach ($collection as $collections) {
            $helper = $objectManager->create('\Psr\Log\LoggerInterface');
                $helper->debug('debug1234');
                $helper->debug(print_r($collections->getData(), true));
        }

        if ($archivedCount > 0) {
            try {
                foreach ($collection as $collections) {
                    $senderEmail = "sender_address@example.com";
                    $senderName = "Sender Name";
                    $recipientEmail = $collections->getCustomerEmail();
                    $incrimentID = $collections->getIncrementId();

                    $transport = $this->transportBuilder
                    ->setTemplateIdentifier('email_section_sendmail_email_template')
                    ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
                    ->setTemplateVars([
                        'templateVar'  => $incrimentID,
                    ])
                
                        ->setFrom(['name' => $senderName,'email' => $senderEmail])
                        ->addTo([$recipientEmail])
                        ->getTransport();
                    $transport->sendMessage();
                }
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $this->messageManager->addSuccess(__('Archive Email has been sent successfully.'));
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            } catch (\Exception $e) {
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $this->messageManager->addError(__('Something went wrong. Please try again later.'));
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }
            $this->messageManager->addSuccess(__('We archived %1 order(s).', $archivedCount));

        } else {
            $this->messageManager->addWarning(__("We can't archive the selected order(s)."));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('sales/order/');

        return $resultRedirect;
    }
}
