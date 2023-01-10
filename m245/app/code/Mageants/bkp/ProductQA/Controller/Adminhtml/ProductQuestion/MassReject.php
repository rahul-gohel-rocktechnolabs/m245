<?php
 /**
  * @category  Mageants ProductQA
  * @package   Mageants_ProductQA
  * @copyright Copyright (c) 2017 Mageants
  * @author    Mageants Team <info@mageants.com>
  */
namespace Mageants\ProductQA\Controller\Adminhtml\ProductQuestion;

use \Magento\Ui\Component\MassAction\Filter;
use \Mageants\ProductQA\Model\ResourceModel\ProductQuestion\CollectionFactory;
use \Magento\Backend\App\Action\Context;
use Mageants\ProductQA\Model\Source\Status;

class MassReject extends \Magento\Backend\App\Action
{
    /**
     * Access Resource ID
     *
     */
    public const RESOURCE_ID = 'Mageants_ProductQA::productquestion_massreject';
    /**
     * Mass Action Filter
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $_filter;

    /**
     * @var \Mageants\ProductQA\Model\ResourceModel\ProductQuestion\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * constructor
     *
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Context $context
     */
    public function __construct(
        Filter $filter,
        CollectionFactory $collectionFactory,
        Context $context
    ) {
        $this->_filter            = $filter;
        
        $this->_collectionFactory = $collectionFactory;
        
        parent::__construct($context);
    }
    /**
     * Check permission via ACL resource
     *
     * @return boolean [description]
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::RESOURCE_ID);
    }
    
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());

        $reject = 0;
        
        foreach ($collection as $productquestion) {
            /** @var \Mageants\ProductQA\Model\ProductQuestion $productquestion */
            $productquestion->setStatus(Status::STATUS_REJECT)->save();
            
            $reject++;
        }
        
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been rejected.', $reject));
        
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        
        return $resultRedirect->setPath('*/*/');
    }
}
