<?php
/**
 * @category Mageants FastOrder
 * @package Mageants_FastOrder
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\FastOrder\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class show extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
   
      /**
       * @var \Magento\Store\Model\StoreManagerInterface
       */

    protected $_storeManager;
    
    /**
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Mageants\FastOrder\Helper\Data $helper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Customer\Source\Group $customerGroup
     * @param \Magento\Customer\Model\SessionFactory $customerSessionFactory
     */
   public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
       
      
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    
    /**
     * Return page factory
     */
    public function execute()
    {
        $data=$this->getRequest()->getPostValue();
        $yourreward = $data['apply_amount'];
        $this->messageManager->addError(
                    __('Maximum '.$yourreward .' rows allowed.')
                );
    }
}
