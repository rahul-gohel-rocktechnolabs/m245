<?php
/**
 * @category Mageants FastOrder
 * @package Mageants_FastOrder
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\FastOrder\ViewModel;

class Custom implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
   
/**
 * @var Magento\Customer\Model\SessionFactory
 */
    protected $customerSessionFactory;

/**
 * @param \Magento\Framework\App\Helper\Context $context
 * @param \Magento\Customer\Model\SessionFactory $customerSessionFactory
 */

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory
    ) {
      
        $this->scopeConfig = $context->getScopeConfig();
        $this->customerSessionFactory = $customerSessionFactory;
    }
     /**
      * Get Store Config Value
      *
      * @param int|null $configPath
      * @return string
      */

    public function getFastOrderConfig($configPath)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
     /**
      * Get Customer SessionFactory
      *
      * * @return void
      */
    public function getSessionData()
    {
        $customerSession = $this->customerSessionFactory->create();
        if (!$customerSession->isLoggedIn()) {
            return 0;
        } else {
            return $customerSession->getCustomer()->getGroupId();
        }
    }
}
