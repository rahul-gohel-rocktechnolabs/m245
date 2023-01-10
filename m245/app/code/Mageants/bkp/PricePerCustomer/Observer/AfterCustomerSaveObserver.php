<?php
/**
 * @category Mageants PricePerCustomer
 * @package Mageants_PricePerCustomer
 * @copyright Copyright (c) 2020 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\PricePerCustomer\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mageants\PricePerCustomer\Model\PricePerCustomer;

/**
 * AfterCustomerSaveObserver class for customer price amount update on after customer save
 */
class AfterCustomerSaveObserver implements ObserverInterface
{
    /**
     * @param \Mageants\PricePerCustomer\Model\PricePerCustomerFactory $pricepercustomerfactory
     */
    public function __construct(
        \Mageants\PricePerCustomer\Model\PricePerCustomerFactory $pricepercustomerfactory
    ) {
        $this->customerpricefactory = $pricepercustomerfactory;
    }

    /**
     * Execute and perform price for customer,
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //customer price data insert
        $deletemodel = $this->customerpricefactory->create();
        $customer = $observer->getCustomer();
        $request  = $observer->getEvent()->getRequest();
        $customer_id = $customer->getId();
        $email = $customer->getEmail();

        $postData = $request->getPostValue();
        $global_customer_price = $postData['global_customer_price'];
        $global_special_price = $postData['global_special_price'];
        $product_wise_price = json_decode($postData['select_product_price'], true);
        $product_changes = json_decode($postData['product_changes'], true);

        $product_data = [];
        if (!empty($product_changes)) {
            foreach ($product_changes as $key => $value) {
                $product_data[] = $value;
            }
        }
        
        //delete data from customer_price
        $customerpricecollection = $this->customerpricefactory->create()->getCollection()
                //->addFieldToSelect('id')
                ->addFieldToFilter('customer_id', $customer_id);
        $price_data = $customerpricecollection->getData();
        if (!empty($price_data)) {
            foreach ($price_data as $key => $value) {
                if (in_array($value['product_id'], $product_data) || $value['product_id'] == 'all') {
                    $price_id = $value['id'];
                    $deletemodel->load($price_id);
                    $deletemodel->delete();
                }
            }
        }

        $model = $this->customerpricefactory->create();
        $model->setCustomerId($data['customer_id'] = $customer_id);
        $model->setProductId($data['product_id'] = 'all');
        $model->setCustomerEmail($data['customer_email'] = $email);
        $model->setCustomerPrice($data['customer_price'] = $global_customer_price);
        $model->setSpecialPrice($data['special_price'] = $global_special_price);
        $model->setData($data);
        $model->save();
      
        if (!empty($product_wise_price)) {
            foreach ($product_wise_price as $pagekey => $pagevalue) {
                foreach ($pagevalue as $key => $value) {
                    if (!empty($value['price']) || !empty($value['special_price'])) {
                        $model->setCustomerId($data['customer_id'] = $customer_id);
                        $model->setProductId($data['product_id'] = $key);
                        $model->setCustomerEmail($data['customer_email'] = $email);
                        $model->setCustomerPrice($data['customer_price'] = $value['price']);
                        $model->setSpecialPrice($data['special_price'] = $value['special_price']);
                        $model->setData($data);
                        $model->save();
                    }
                }
            }
        }
    }
}
