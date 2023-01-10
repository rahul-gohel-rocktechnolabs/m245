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
 * Addcustomerprice class for add customer price in custom table
 */
class Addcustomerprice extends \Magento\Framework\App\Action\Action
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
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Mageants\PricePerCustomer\Model\PricePerCustomerFactory $customerpricefactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Mageants\PricePerCustomer\Model\PricePerCustomerFactory $customerpricefactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerpricefactory = $customerpricefactory;
    }

    /**
     * Execute
     *
     * @return result
     */
    public function execute()
    {
        $postdata = $this->getRequest()->getPostValue();
        $result_return = $this->resultJsonFactory->create();
        $model = $this->customerpricefactory->create();

        $customer_id = $postdata['customer_id'];
        $product_id = $postdata['product_id'];
        $email = trim($postdata['email']);
        $price = trim($postdata['price']);
        $special_price = trim($postdata['special_price']);

        if (($email == '') || ($price == '' && $special_price == '')) {
            $msg = 'Empty data for customer data. Please update data for saving';
            $result=[0=>'0',1=>$msg];
            return $result_return->setData($result);
        } else {
            if (!empty($customer_id) && !empty($product_id)) {
                $customerpricecollection = $this->customerpricefactory->create()->getCollection()
                ->addFieldToSelect('id')
                ->addFieldToFilter('customer_id', $customer_id)
                ->addFieldToFilter('product_id', $product_id);
                $price_data = $customerpricecollection->getData();
                if (!empty($price_data)) {
                     $price_id = $price_data[0]['id'];
                } else {
                    $price_id = '';
                }
            }
            if (!empty($postdata['id'])) {
                $model->load($postdata['id']);
                $model->setId($data['id'] = $postdata['id']);
            } elseif (!empty($price_id)) {
                $model->load($price_id);
                $model->setId($data['id'] = $price_id);
            }
            $model->setCustomerId($data['customer_id'] = $customer_id);
            $model->setProductId($data['product_id'] = $product_id);
            $model->setCustomerEmail($data['customer_email'] = $email);
            $model->setCustomerPrice($data['customer_price'] = $price);
            $model->setSpecialPrice($data['special_price'] = $special_price);
            $model->setData($data);
            $model->save();

            $msg = 'sucess';
            $result=[0=>'1',1=>$msg];
            return $result_return->setData($result);
        }
    }
}
