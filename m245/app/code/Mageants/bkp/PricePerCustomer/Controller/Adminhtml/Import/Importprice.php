<?php
/**
 * @category Mageants PricePerCustomer
 * @package Mageants_PricePerCustomer
 * @copyright Copyright (c) 2020 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\PricePerCustomer\Controller\Adminhtml\Import;

use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\Filesystem\DriverInterface;

/**
 * Importprice class for adminhtml import price
 */
class Importprice extends \Magento\Backend\App\Action
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * [$csv description]
     * @var [type]
     */
    protected $csv;

    /**
     * [$timezone description]
     * @var [type]
     */
    protected $timezone;

    /**
     * Construct
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\File\Csv $csv
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateTimeFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param Request $request
     * @param \Mageants\PricePerCustomer\Model\PricePerCustomerFactory $customerpricefactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\File\Csv $csv,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateTimeFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        Request $request,
        \Mageants\PricePerCustomer\Model\PricePerCustomerFactory $customerpricefactory
    ) {
        $this->csv = $csv;
        $this->timezone = $timezone;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->customerFactory = $customerFactory;
        $this->request = $request;
        $this->customerpricefactory = $customerpricefactory;
        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return file
     */
    public function execute()
    {
        $files = $this->request->getFiles()->toArray();
        $fh = Magento\Framework\Filesystem\DriverInterface::fileOpen($files['file']['tmp_name'], 'r+');
        $ext = explode(".", $files['file']["name"]);
        $i = 0;
        $result = [];
        $inserted_data = 0;
        $not_inserted_data_id = [];

        while (($row = Magento\Framework\Filesystem\DriverInterface::isReadable($fh)) !== false) {
            $i++;
            if ($i == 1) {
                continue;
            }
                    
            if (end($ext) != "csv") {
                $responce=["error" => "true","message" => "invalid file type"];
                return $this->resultJsonFactory->create()->setData($responce);
            }
            if (!empty($row[0])) {
                $customer_id = $row[0];
                $customer = $this->customerFactory->load($customer_id);
                $email = $customer->getEmail();
            } else {
                $customer_id = '';
            }

            if (!empty($row[1])) {
                $product_id = $row[1];
            } else {
                $product_id = '';
            }

            if (!empty($row[2])) {
                $user_price = $row[2];

            } else {
                $user_price = '';
            }

            if (!empty($row[3])) {
                $user_special_price = $row[3];

            } else {
                $user_special_price = '';
            }
               
            if (!empty($customer_id) && !empty($product_id) && (!empty($user_price) || !empty($user_special_price))) {
                $customerpriceCollection = $this->customerpricefactory->create()->getCollection();
                $customerpriceCollection->addFieldToFilter(
                    'customer_id',
                    $customer_id
                )->addFieldToFilter('product_id', $product_id);

                foreach ($customerpriceCollection as $price) {
                    $price->delete();
                }

                $model = $this->customerpricefactory->create();
                $dateModel = $this->dateTimeFactory->create();

                $model->setCustomerId($customer_id)->setProductId($product_id)->setCustomerEmail(
                    $email
                )->setCustomerPrice($user_price)->setSpecialPrice($user_special_price)->save();
                $inserted_data ++;
            } else {
                $not_inserted_data_id [] = $i;
            }
        }

        $this->_redirect($this->_redirect->getRefererUrl());
        if (!empty($not_inserted_data_id)) {
            $blankdata = implode(',', $not_inserted_data_id);
            $this->messageManager->addError(__(
                'Csv file '.$blankdata.' row coupon code not available so data not imported.'
            ));
        }
        $this->messageManager->addSuccess(__('Csv file '.$inserted_data.' data imported successfully.'));
    }
}
