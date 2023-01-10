<?php
/**
 * @category Mageants PricePerCustomer
 * @package Mageants_PricePerCustomer
 * @copyright Copyright (c) 2020 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\PricePerCustomer\Plugin\Model;
 
class Product
{
    /**
     * [$_customerSession description]
     * @var [type]
     */
    protected $_customerSession;

    /**
     * Construct
     *
     * @param \Mageants\PricePerCustomer\Model\PricePerCustomerFactory $pricepercustomerfactory
     * @param \Magento\Customer\Model\SessionFactory $session
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Mageants\PricePerCustomer\Model\PricePerCustomerFactory $pricepercustomerfactory,
        \Magento\Customer\Model\SessionFactory $session,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->customerpricefactory = $pricepercustomerfactory;
        $this->session = $session;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get Price
     *
     * @param  \Magento\Catalog\Model\Product $subject
     * @param  result $result
     * @return result
     */
    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        // return $result;
        $customer_price_enable = $this->scopeConfig->getValue(
            'pricepercustomer/general/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        
        if ($customer_price_enable == 1) {
            $price = 0;
            $customerSession = $this->session->create();

            $customer_id = '0';
            if (!empty($customerSession->getCustomer()->getId())) {
                $customer_id = $customerSession->getCustomer()->getId();
            } else {
                return $result;
            }

            $product_id = $subject->getId();

            //get customer price
            $customerpricecollection = $this->customerpricefactory->create()->getCollection()
                    ->addFieldToFilter('customer_id', $customer_id)
                    ->addFieldToFilter(
                        'product_id',
                        [[$product_id, 'all']]
                    );
            $price_data = $customerpricecollection->getData();
            if (!empty($price_data)) {
                foreach ($price_data as $key => $value) {
                    if ($value['product_id'] == 'all') {
                        $all_product_price = $value['customer_price'];
                    } else {
                        $product_price = $value['customer_price'];
                    }
                }
                if (!empty($product_price)) {
                    $price = $product_price;
                } elseif (!empty($all_product_price)) {
                    $price = $all_product_price;
                }
            }
            if (strpos($price, '%') !== false) {
                $customerwise_price = str_replace("%", "", $price);
                $percentage_price = $result*$customerwise_price/100;
                $result += $percentage_price;
            } else {
                $result += $price;
            }
        }
        return $result;
    }

    /**
     * Get Special Price
     *
     * @param  \Magento\Catalog\Model\Product $subject
     * @param  result $result
     * @return result
     */
    public function aftergetSpecialPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        // return $result;
        $customer_price_enable = $this->scopeConfig->getValue(
            'pricepercustomer/general/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($customer_price_enable == 1) {
            $price = 0;
            $customerSession = $this->session->create();

            $customer_id = '0';
            if ($customerSession->isLoggedIn()) {
                $customer_id = $customerSession->getCustomer()->getId();
            } else {
                return $result;
            }
            
            $product_id = $subject->getId();

            //get customer price
            $customerpricecollection = $this->customerpricefactory->create()->getCollection()
                    ->addFieldToFilter('customer_id', $customer_id)
                    ->addFieldToFilter(
                        'product_id',
                        [[$product_id, 'all']]
                    );
            $price_data = $customerpricecollection->getData();
            if (!empty($price_data)) {
                foreach ($price_data as $key => $value) {
                    if ($value['product_id'] == 'all') {
                        $all_product_price = $value['special_price'];
                    } else {
                        $product_price = $value['special_price'];
                    }
                }
                if (!empty($product_price)) {
                    $price = $product_price;
                } elseif (!empty($all_product_price)) {
                    $price = $all_product_price;
                }
            }
            if (!empty($result)) {
                if (strpos($price, '%') !== false) {
                    $customerwise_price = str_replace("%", "", $price);
                    $percentage_price = $result*$customerwise_price/100;
                    $result += $percentage_price;
                } else {
                    $result += $price;
                }
            }
        }
        return $result;
    }
}
