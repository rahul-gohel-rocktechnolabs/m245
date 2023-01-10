<?php
/**
 * @category Mageants PricePerCustomer
 * @package Mageants_PricePerCustomer
 * @copyright Copyright (c) 2020 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\PricePerCustomer\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

/**
 * Customerpricegrid class for adminhtml grid filter result
 */
class Customerpricegrid extends \Magento\Backend\App\Action
{
    /**
     * [$resultLayoutFactory description]
     * @var [type]
     */
    private $resultLayoutFactory;

    /**
     * Construct
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * IsAllwoed
     *
     * @return boolean
     */
    public function _isAllowed()
    {
        return true;
    }

    /**
     * Execute
     *
     * @return result
     */
    public function execute()
    {
        $resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('pricepercustomer.product.edit');

        return $resultLayout;
    }
}
