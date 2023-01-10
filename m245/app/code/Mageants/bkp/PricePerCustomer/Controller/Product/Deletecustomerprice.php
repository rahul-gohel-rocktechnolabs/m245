<?php
/**
 * @category Mageants PricePerCustomer
 * @package Mageants_PricePerCustomer
 * @copyright Copyright (c) 2020 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\PricePerCustomer\Controller\Product;

use Magento\Backend\App\Action;

/**
 * Deletecustomerprice class for delete customer price in custom table
 */
class Deletecustomerprice extends \Magento\Framework\App\Action\Action
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
     * @param \Mageants\PricePerCustomer\Model\PricePerCustomerFactory $customerpricefactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Mageants\PricePerCustomer\Model\PricePerCustomerFactory $customerpricefactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->customerpricefactory = $customerpricefactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Execute
     *
     * @return result
     */
    public function execute()
    {
        $result_return = $this->resultJsonFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data['id']) {
            $model = $this->customerpricefactory->create();
            $model->load($data['id']);
            $model->delete();
            $result=[0=>'1'];
            return $result_return->setData($result);
        } else {
            $result=[0=>'0'];
            return $result_return->setData($result);
        }
    }
}
