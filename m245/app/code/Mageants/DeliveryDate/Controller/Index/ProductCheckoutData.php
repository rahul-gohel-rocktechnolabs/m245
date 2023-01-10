<?php

/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\DeliveryDate\Controller\Index;

class ProductCheckoutdata extends \Magento\Framework\App\Action\Action
{
    /**
     * @var resultJsonFactory
     */
    public $resultJsonFactory;
    /**
     * @var request
     */
    public $request;
    /**
     * @var helper
     */
    public $helper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Mageants\DeliveryDate\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Mageants\DeliveryDate\Helper\Data $helper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->product = $product;
        $this->serializer = $serializer;
        $this->request = $request;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * Main function
     *
     * @return void
     */
    public function execute()
    {
        if ($this->helper->getPluginDisplayAt() == 3) {
            if ($this->request->getPost('deliverydate') != null) {
                $deliverydate = $this->serializer->unserialize($this->request->getPost('deliverydate'));
                $deliverytime = $this->serializer->unserialize($this->request->getPost('deliverytime'));
                $deliverycomment = $this->serializer->unserialize($this->request->getPost('deliverycomment'));
                $dateDeliveryData = [];
                foreach ($deliverydate as $key => $productId) {
                    $product = $this->product->load($productId['item_id']);
                    $dateDeliveryData[$key]['productName'] =  $product->getName();
                    $dateDeliveryData[$key]['deliverydate'] = $deliverydate[$key]['delivery_date'];
                    $dateDeliveryData[$key]['deliverytime'] = $deliverytime[$key]['delivery_timeslot'];
                    $dateDeliveryData[$key]['deliverycomment'] = $deliverycomment[$key]['delivery_comment'];
                }

                $result = $this->resultJsonFactory->create();
                return $result->setData($this->serializer->serialize($dateDeliveryData));
            }
        }
    }
}
