<?php
 /**
  * @category  Mageants ProductQA
  * @package   Mageants_ProductQA
  * @copyright Copyright (c) 2017 Mageants
  * @author    Mageants Team <info@mageants.com>
  */
namespace Mageants\ProductQA\Controller\Adminhtml\ProductQuestionAnswer;

use \Magento\Ui\Component\MassAction\Filter;
use \Mageants\ProductQA\Model\ResourceModel\ProductQuestionAnswer\CollectionFactory;
use \Magento\Backend\App\Action\Context;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Access Resource ID
     *
     */
    public const RESOURCE_ID = 'Mageants_ProductQA::productanswer_massdel';
    /**
     * Mass Action Filter
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $_filter;

    /**
     * @var \Mageants\ProductQA\Model\ResourceModel\ProductQuestionAnswer\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Constructor
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

        $delete = 0;
        
        foreach ($collection as $productquestionanswer) {
            /** @var \Mageants\ProductQA\Model\ProductQuestionAnswer $productquestionanswer */
            $productquestionanswer->delete();
            
            $delete++;
        }
        
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $delete));
        
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        
        return $resultRedirect->setPath('*/*/');
    }
}
