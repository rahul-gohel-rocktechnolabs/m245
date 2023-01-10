<?php
/**
 * @category Mageants PricePerCustomer
 * @package Mageants_PricePerCustomer
 * @copyright Copyright (c) 2020 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\PricePerCustomer\Controller\Index;

use Magento\Backend\App\Action;

/**
 * Grid class for adminhtml grid
 */
class Grid extends \Magento\Backend\App\Action
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
     * IsAllowed
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
        $gridBlock = $resultLayout->getLayout()->getBlock(
            \Mageants\PricePerCustomer\Block\Adminhtml\Product\Edit\CustomerPriceTab::class
        );
        
        return $resultLayout;
    }
}
