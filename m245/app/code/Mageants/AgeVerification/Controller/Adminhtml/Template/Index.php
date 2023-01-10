<?php
/**
 * @category Mageants AgeVerification
 * @package Mageants_AgeVerification
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <info@mageants.com>
 */
namespace Mageants\AgeVerification\Controller\Adminhtml\Template;

class Index extends \Magento\Backend\App\Action
{
    public const ADMIN_RESOURCE = 'Mageants_AgeVerification::template';
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory = false;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
   /**
    * Undocumented function
    *
    * @return void
    */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Age Verification Templates')));

        return $resultPage;
    }
}
