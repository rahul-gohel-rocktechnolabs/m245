<?php
/**
 * @category Mageants ImageGallery
 * @package Mageants_ImageGallery
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ImageGallery\Controller\Adminhtml\Gallery;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Admin resource
     */
    public const ADMIN_RESOURCE = 'Mageants_ImageGallery::delete';
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var \Mageants\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param \Mageants\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Mageants\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        
        foreach ($collection as $block) {
            $block->delete();
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
