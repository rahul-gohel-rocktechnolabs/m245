<?php
 /**
  * @category  Mageants ProductQA
  * @package   Mageants_ProductQA
  * @copyright Copyright (c) 2017 Mageants
  * @author    Mageants Team <info@mageants.com>
  */
namespace Mageants\ProductQA\Controller\Adminhtml\ProductQuestionAnswer;

use \Magento\Backend\Model\Session;
use \Magento\Framework\View\Result\PageFactory;
use \Magento\Framework\Controller\Result\JsonFactory;
use \Mageants\ProductQA\Model\ProductQuestionAnswerFactory;
use \Magento\Framework\Registry;
use \Magento\Backend\Model\View\Result\RedirectFactory;
use \Magento\Backend\App\Action\Context;

class Edit extends \Mageants\ProductQA\Controller\Adminhtml\ProductQuestionAnswer
{
    /**
     * Access Resource ID
     *
     */
    public const RESOURCE_ID = 'Mageants_ProductQA::productanswer_new';
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * Page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

  /**
   * Constructor
   *
   * @param PageFactory                  $resultPageFactory            [description]
   * @param JsonFactory                  $resultJsonFactory            [description]
   * @param ProductQuestionAnswerFactory $productquestionanswerFactory [description]
   * @param Registry                     $registry                     [description]
   * @param Context                      $context                      [description]
   */
    public function __construct(
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        ProductQuestionAnswerFactory $productquestionanswerFactory,
        Registry $registry,
        Context $context
    ) {
        $this->_backendSession = $context->getSession();
        
        $this->_resultPageFactory = $resultPageFactory;
        
        $this->_resultJsonFactory = $resultJsonFactory;
        
        parent::__construct($productquestionanswerFactory, $registry, $context);
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
     * Execute
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        
        /** @var \Mageants\ProductQA\Model\ProductQuestionAnswer $productquestionanswer */
        $productquestionanswer = $this->_initProductQuestionAnswer();
        
        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        
        $resultPage->setActiveMenu('Mageants_ProductQA::productquestionanswer');
        
        $resultPage->getConfig()->getTitle()->set(__(' Product Question'));
        
        if ($id) {
            $productquestionanswer->load($id);
            
            if (!$productquestionanswer->getId()) {
                $this->messageManager->addError(__('This ProductQuestionAnswer no longer exists.'));
                
                $resultRedirect = $this->_resultRedirectFactory->create();
                
                $resultRedirect->setPath(
                    'mageants_productqa/*/edit',
                    [
                        'id' => $productquestionanswer->getId(),
                        '_current' => true
                    ]
                );
                
                return $resultRedirect;
            }
        }
        
        $title = $productquestionanswer->getId() ? $productquestionanswer->getName() : __('New Product Question');
        
        $resultPage->getConfig()->getTitle()->prepend($title);
        
        $data = $this->_backendSession->getData('mageants_productqa_productquestionanswer_data', true);
        
        if (!empty($data)) {
            $productquestionanswer->setData($data);
        }
        
        return $resultPage;
    }
}
