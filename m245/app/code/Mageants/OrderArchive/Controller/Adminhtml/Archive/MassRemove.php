<?php
/**
 * @category Mageants OrderArchive
 * @package Mageants OrderArchive
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\OrderArchive\Controller\Adminhtml\Archive;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * Class MassRemove
 */
class MassRemove extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mageants_OrderArchive::remove';

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
        $archivedIds = $this->_archiveModel->removeOrdersFromArchiveById($collection->getAllIds());
        $archivedCount = count($archivedIds);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $orderIds = implode(', ', $collection->getAllIds());

        if ($archivedCount > 0) {
            try {
                foreach ($collection as $collections) {
                       $senderEmail = "sender_address@example.com";
                       $senderName = "Sender Name";
                       $recipientEmail = $collections->getCustomerEmail();
                       $incrimentID = $collections->getIncrementId();


                       $transport = $this->transportBuilder
                    ->setTemplateIdentifier('email_section_unarchive')
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
                $this->messageManager->addSuccess(__('UnArchive Email has been sent successfully.'));
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong. Please try again later.'));
                $resultRedirect->setPath('sales/archive/orders');
                return $resultRedirect;
            }
            $this->messageManager->addSuccess(__('We removed %1 order(s) from the archive.', $archivedCount));
        }
    }
}
