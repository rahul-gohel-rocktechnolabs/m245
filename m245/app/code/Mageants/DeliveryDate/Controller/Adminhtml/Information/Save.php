<?php

/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\DeliveryDate\Controller\Adminhtml\Information;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\TestFramework\ErrorLog\Logger;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order;
use Magento\Framework\Controller\ResultFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var jshelper
     */
    protected $_jsHelper;
    /**
     * @var orderResouceModel
     */
    protected $_orderResourceModel;
    /**
     * @var orderRepository
     */
    protected $_orderRepository;
    /**
     * @var helper
     */
    protected $helper;
    /**
     * @var order
     */
    protected $order;
    /**
     * @var resultFactory
     */
    protected $resultFactory;
    /**
     * @var redirect
     */
    protected $redirect;

    /**
     * @param Context $context
     * @param \Magento\Backend\Helper\Js $jsHelper
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Model\ResourceModel\Order $orderResourceModel
     * @param \Mageants\DeliveryDate\Helper\Data $helper
     * @param \Magento\Sales\Model\Order $order
     * @param ResultFactory $resultFactory
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        Context $context,
        \Magento\Backend\Helper\Js $jsHelper,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\ResourceModel\Order $orderResourceModel,
        \Mageants\DeliveryDate\Helper\Data $helper,
        \Magento\Sales\Model\Order $order,
        ResultFactory $resultFactory,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->_jsHelper = $jsHelper;
        parent::__construct($context);
        $this->_orderResourceModel = $orderResourceModel;
        $this->_orderRepository = $orderRepository;
        $this->_helper = $helper;
        $this->order = $order;
        $this->resultFactory = $resultFactory;
        $this->redirect = $redirect;
        $this->_resource = $resource;
        $this->quoteFactory = $quoteFactory;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    /**
     * Main Execute function
     *
     * @return void
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('id');
        $data = $this->getRequest()->getPostValue();
        $today = date("Y-m-d");
        $displayAt = $this->_helper->getCustomModelData($orderId);

        if (!isset($data['delivery_comment'])) {
            $data['delivery_comment'] = '';
        }

        if ($displayAt == 3) {
            $orderData = $this->order->load($orderId);
            if ($orderData->getData('status') == 'complete') {
                $message = 'Order completed.You can not Edit this record.';
                $this->messageManager->addError($message);
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setUrl($this->redirect->getRefererUrl());

                return $resultRedirect;
            } else {
                $delivery_date = json_decode($orderData->getData('delivery_date'));
                $delivery_time = json_decode($orderData->getData('delivery_timeslot'));
                $delivery_comment = json_decode($orderData->getData('delivery_comment'));
                if ($orderData->getData('delivery_status') != null) {
                    $delivery_status = json_decode($orderData->getData('delivery_status'));
                }
                foreach ($delivery_date as $key => $value) {
                    if ($delivery_date[$key]->item_id == $data['product_id']) {
                        $delivery_date[$key]->delivery_date = $data['delivery_date'];
                        $delivery_time[$key]->delivery_timeslot = $data['delivery_timeslot'];
                        $delivery_comment[$key]->delivery_comment = $data['delivery_comment'];
                        if (isset($delivery_status)) {
                            $delivery_status[$key]->delivery_status = $data['delivery_status'];
                        }
                    }
                }
                $delivery_dat_arr = json_encode($delivery_date);
                $delivery_time_arr = json_encode($delivery_time);
                $delivery_comment_arr = json_encode($delivery_comment);
                if (isset($delivery_status)) {
                    $delivery_status_arr = json_encode($delivery_status);
                }

                $resultRedirect = $this->resultRedirectFactory->create();

                $resource = $this->_resource;
                $connection = $resource->getConnection();
                $salesorder = $resource->getTableName('sales_order');

                $salesordergrid = $resource->getTableName('sales_order_grid');

                if (isset($delivery_status_arr)) {
                    $salesordersql = "Update " . $salesorder . " Set delivery_date = '"
                        . $delivery_dat_arr . "',delivery_comment='" . $delivery_comment_arr . "' ,delivery_timeslot='"
                        . $delivery_time_arr . "' ,delivery_status='" . $delivery_status_arr . "' where entity_id ='"
                        . $orderId . "'";
                    $connection->query($salesordersql);
                }

                $salesordergridsql = "Update " . $salesordergrid . " Set delivery_date = '"
                    . $delivery_dat_arr . "',delivery_comment='"
                    . $delivery_comment_arr . "' ,delivery_timeslot='" . $delivery_time_arr
                    . "' where entity_id ='" . $orderId . "'";
                $connection->query($salesordergridsql);

                $modelOrder = $this->order->load($orderId);
                $modelQuote = $this->quoteFactory->create()->load($modelOrder->getQuoteId());
                try {
                    $modelQuote->setDeliveryDate($delivery_dat_arr);
                    $modelQuote->setDeliveryComment($delivery_comment_arr);
                    $modelQuote->setDeliveryTimeslot($delivery_time_arr);
                    $modelQuote->save();

                    $modelOrder->setData('delivery_date', $delivery_dat_arr);
                    $modelOrder->setData('delivery_comment', $delivery_comment_arr);
                    $modelOrder->setData('delivery_timeslot', $delivery_time_arr);
                    if (isset($delivery_status_arr)) {
                        $modelOrder->setData('delivery_status', $delivery_status_arr);
                    }
                    $modelOrder->save();
                    $this->messageManager->addSuccess(__('You updated the Delivery Information.'));
                    return $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while saving the item.'));
                }
            }
        } else {
            $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
            $metadata->setPath('/');
            $this->cookieManager->deleteCookie(
                'delivery_date',
                $metadata
            );
            $this->cookieManager->deleteCookie(
                'delivery_comment',
                $metadata
            );
            $this->cookieManager->deleteCookie(
                'delivery_timeslot',
                $metadata
            );
            $resultRedirect = $this->resultRedirectFactory->create();
            $id = $this->getRequest()->getParam('id');
            $resource = $this->_resource;
            $connection = $resource->getConnection();
            $salesorder = $resource->getTableName('sales_order');

            $salesordergrid = $resource->getTableName('sales_order_grid');

            $salesordersql = "Update " . $salesorder . " Set delivery_date = '" . $data['delivery_date']
                . "',delivery_comment='" . $data['delivery_comment'] . "' ,delivery_timeslot='"
                . $data['delivery_timeslot'] . "' where entity_id ='" . $id . "'";
            $connection->query($salesordersql);

            $salesordergridsql = "Update " . $salesordergrid . " Set delivery_date = '" . $data['delivery_date']
                . "',delivery_comment='" . $data['delivery_comment'] . "' ,delivery_timeslot='"
                . $data['delivery_timeslot']. "' where entity_id ='" . $id . "'";
            $connection->query($salesordergridsql);
            $modelOrder = $this->order->load($orderId);
            $modelQuote = $this->quoteFactory->create()->load($modelOrder->getQuoteId());

            try {
                $modelQuote->setDeliveryDate($data['delivery_date']);
                $modelQuote->setDeliveryComment($data['delivery_comment']);
                $modelQuote->setDeliveryTimeslot($data['delivery_timeslot']);
                $modelQuote->save();

                $modelOrder->setData('delivery_date', $data['delivery_date']);
                $modelOrder->setData('delivery_comment', $data['delivery_comment']);
                $modelOrder->setData('delivery_timeslot', $data['delivery_timeslot']);
                $modelOrder->save();
                $deliveryDate = $data['delivery_date'];
                $deliveryComment = $data['delivery_comment'];
                $deliveryTime = $data['delivery_timeslot'];
               
                $this->messageManager->addSuccess(__('You updated the Delivery Information.'));
                return $resultRedirect->setPath('sales/order/view', ['order_id' => $id]);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the item.'));
            }
        }
    }
}
