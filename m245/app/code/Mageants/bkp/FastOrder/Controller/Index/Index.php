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

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
     /**
      * @var \Mageants\FastOrder\Helper\Data
      */
    protected $_helper;
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
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Mageants\FastOrder\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Customer\Source\Group $customerGroup,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_helper = $helper;
        $this->_storeManager = $storeManager;
        $this->customerGroup = $customerGroup;
        $this->customerSessionFactory = $customerSessionFactory;
        parent::__construct($context);
    }
    
    /**
     * Return page factory
     */
    public function execute()
    {

        $customerSession = $this->customerSessionFactory->create();
        $cust_group_conf = explode(',', $this->_helper->getFastOrderConfig('fastorder/general/enable_customer_group'));

        if (!$customerSession->isLoggedIn()) {
            $customerGroupId = 0;
        } else {
            $customerGroupId = $customerSession->getCustomer()->getGroupId();
        }
        if (!in_array($customerGroupId, $cust_group_conf)) {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            if (in_array(32000, $cust_group_conf)) {
                $resultPage = $this->_resultPageFactory->create();
                return $resultPage;
            } else {
                $resultRedirect->setPath($this->_storeManager->getStore()->getBaseUrl());
                return $resultRedirect;
            }
        }

        $resultPage = $this->_resultPageFactory->create();
        return $resultPage;
    }
}
