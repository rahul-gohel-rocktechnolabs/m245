<?php
 /**
  * @category  Mageants ProductQA
  * @package   Mageants_ProductQA
  * @copyright Copyright (c) 2017 Mageants
  * @author    Mageants Team <info@mageants.com>
  */
namespace Mageants\ProductQA\Controller\Adminhtml;

use \Mageants\ProductQA\Model\ProductQuestionFactory;
use \Magento\Framework\Registry;
use \Magento\Backend\Model\View\Result\RedirectFactory;
use \Magento\Backend\App\Action\Context;
        
abstract class ProductQuestion extends \Magento\Backend\App\Action
{
    /**
     * Post Factory
     *
     * @var \Mageants\ProductQA\Model\ProductQuestionFactory
     */
    protected $_productquestionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Backend\Model\View\Result\RedirectFactory
     */
    protected $_resultRedirectFactory;

   /**
    * Constructor
    *
    * @param ProductQuestionFactory $productquestionFactory [description]
    * @param Registry               $coreRegistry           [description]
    * @param Context                $context                [description]
    */
    public function __construct(
        ProductQuestionFactory $productquestionFactory,
        Registry $coreRegistry,
        Context $context
    ) {
        $this->_productquestionFactory           = $productquestionFactory;
        
        $this->_coreRegistry          = $coreRegistry;
        
        $this->_resultRedirectFactory = $context->getResultRedirectFactory();
        
        parent::__construct($context);
    }

    /**
     * Init Post
     *
     * @return \Mageants\ProductQA\Model\ProductQuestion
     */
    protected function _initProductQuestion()
    {
        $productquestionid  = (int) $this->getRequest()->getParam('id');
        
        /** @var \Mageants\ProductQA\Model\ProductQuestion $productquestion */
        $productquestion    = $this->_productquestionFactory->create();
        
        if ($productquestionid) {
            $productquestion->load($productquestionid);
        }
        
        $this->_coreRegistry->register('mageants_productquestion', $productquestion);
        
        return $productquestion;
    }
}
