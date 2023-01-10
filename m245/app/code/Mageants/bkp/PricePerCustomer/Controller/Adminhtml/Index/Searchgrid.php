<?php
/**
 * @category Mageants PricePerCustomer
 * @package Mageants_PricePerCustomer
 * @copyright Copyright (c) 2020 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\PricePerCustomer\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\LayoutFactory;

/**
 * Searchgrid class for adminhtml grid
 */
class Searchgrid extends \Magento\Backend\App\Action
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
     * @param Rawfactory $resultRawFactory
     * @param LayoutFactory $layoutFactory
     */
    public function __construct(
        Context $context,
        Rawfactory $resultRawFactory,
        LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
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
        $resultLayout = $this->resultRawFactory->create();
        return $resultLayout->setContents(
            $this->layoutFactory->create()->createBlock(
                \Mageants\PricePerCustomer\Block\Adminhtml\Grid\Searchgrid::class,
                'rh.custom.tab.productgrid'
            )->toHtml()
        );
    }
}
