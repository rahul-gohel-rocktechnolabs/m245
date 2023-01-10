<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mageants\DeliveryDate\Controller\Adminhtml\Order\Shipment;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\Order\Shipment\Validation\QuantityValidator;

class Save extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_Sales::shipment';

    /**
     * @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader
     */
    protected $shipmentLoader;

    /**
     * @var \Magento\Shipping\Model\Shipping\LabelGenerator
     */
    protected $labelGenerator;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\ShipmentSender
     */
    protected $shipmentSender;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\ShipmentValidatorInterface
     */
    private $shipmentValidator;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader
     * @param \Magento\Shipping\Model\Shipping\LabelGenerator $labelGenerator
     * @param \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender
     * @param \Mageants\DeliveryDate\Helper\Data $helper
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Sales\Model\Order\Shipment\ShipmentValidatorInterface $shipmentValidator
     * @param \Magento\Framework\DB\Transaction $transaction
     * @param \Psr\Log\LoggerInterface $loggerInterface
     * @param \Magento\Backend\Model\SessionFactory $sessionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader,
        \Magento\Shipping\Model\Shipping\LabelGenerator $labelGenerator,
        \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender,
        \Mageants\DeliveryDate\Helper\Data $helper,
        \Magento\Sales\Model\Order $order,
        \Magento\Sales\Model\Order\Shipment\ShipmentValidatorInterface $shipmentValidator,
        \Magento\Framework\DB\Transaction $transaction,
        \Psr\Log\LoggerInterface $loggerInterface,
        \Magento\Backend\Model\SessionFactory $sessionFactory
    ) {
        $this->helper = $helper;
        $this->order = $order;
        parent::__construct($context);

        $this->shipmentLoader = $shipmentLoader;
        $this->labelGenerator = $labelGenerator;
        $this->shipmentSender = $shipmentSender;
        $this->shipmentValidator = $shipmentValidator;
        $this->transaction = $transaction;
        $this->loggerInterface = $loggerInterface;
        $this->sessionFactory = $sessionFactory;
    }

    /**
     * Save shipment and order in one transaction
     *
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @return $this
     */
    protected function _saveShipment($shipment)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $shipment->getOrder()->setIsInProcess(true);
        $transaction = $this->transaction;
        $helper = $this->helper;
        $displayAt = $helper->getCustomModelData($shipment->getOrder()->getId());
        if ($displayAt == 3) {
            $order = $this->order->load($shipment->getOrder()->getId());
            $delivery_status = json_decode($order->getData('delivery_status'));
            $delivery_status_arr = [];
            $i = 0;
            foreach ($delivery_status as $key => $value) {
                $delivery_status_arr[$i] = $delivery_status[$key]->delivery_status;
                $i++;
            }
            if (in_array("pending", $delivery_status_arr)) {
                $orderState = \Magento\Sales\Model\Order::STATE_PROCESSING;
                $order->setState($orderState)->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
                $order->save();
                $transaction->addObject(
                    $shipment
                )->addObject(
                    $order
                )->save();
                return $resultRedirect->setPath('sales/order/index');
            } else {
                $transaction->addObject(
                    $shipment
                )->addObject(
                    $shipment->getOrder()
                )->save();

                return $this;
            }
        }
        $transaction->addObject(
            $shipment
        )->addObject(
            $shipment->getOrder()
        )->save();

        return $this;
    }

    /**
     * Save shipment
     *
     * We can save only new shipment. Existing shipments are not editable
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $formKeyIsValid = $this->_formKeyValidator->validate($this->getRequest());
        $isPost = $this->getRequest()->isPost();
        if (!$formKeyIsValid || !$isPost) {
            $this->messageManager->addErrorMessage(__('We can\'t save the shipment right now.'));
            return $resultRedirect->setPath('sales/order/index');
        }

        $data = $this->getRequest()->getParam('shipment');

        $sessionManager = $this->sessionFactory->create();
        if (!empty($data['comment_text'])) {
            $sessionManager->setCommentText($data['comment_text']);
        }

        $isNeedCreateLabel = isset($data['create_shipping_label']) && $data['create_shipping_label'];
        $responseAjax = new \Magento\Framework\DataObject();

        try {
            $this->shipmentLoader->setOrderId($this->getRequest()->getParam('order_id'));
            $this->shipmentLoader->setShipmentId($this->getRequest()->getParam('shipment_id'));
            $this->shipmentLoader->setShipment($data);
            $this->shipmentLoader->setTracking($this->getRequest()->getParam('tracking'));
            $shipment = $this->shipmentLoader->load();
            if (!$shipment) {
                return $this->resultFactory->create(ResultFactory::TYPE_FORWARD)->forward('noroute');
            }

            if (!empty($data['comment_text'])) {
                $shipment->addComment(
                    $data['comment_text'],
                    isset($data['comment_customer_notify']),
                    isset($data['is_visible_on_front'])
                );

                $shipment->setCustomerNote($data['comment_text']);
                $shipment->setCustomerNoteNotify(isset($data['comment_customer_notify']));
            }
            $validationResult = $this->shipmentValidator->validate($shipment, [QuantityValidator::class]);

            if ($validationResult->hasMessages()) {
                $this->messageManager->addErrorMessage(
                    __("Shipment Document Validation Error(s):\n" . implode("\n", $validationResult->getMessages()))
                );
                return $resultRedirect->setPath('*/*/new', ['order_id' => $this->getRequest()->getParam('order_id')]);
            }
            $shipment->register();

            $shipment->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));

            if ($isNeedCreateLabel) {
                $this->labelGenerator->create($shipment, $this->_request);
                $responseAjax->setOk(true);
            }

            $this->_saveShipment($shipment);

            if (!empty($data['send_email'])) {
                $this->shipmentSender->send($shipment);
            }

            $shipmentCreatedMessage = __('The shipment has been created.');
            $labelCreatedMessage = __('You created the shipping label.');

            $this->messageManager->addSuccessMessage(
                $isNeedCreateLabel ? $shipmentCreatedMessage . ' ' . $labelCreatedMessage : $shipmentCreatedMessage
            );
            $sessionManager->getCommentText(true);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($isNeedCreateLabel) {
                $responseAjax->setError(true);
                $responseAjax->setMessage($e->getMessage());
            } else {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/new', ['order_id' => $this->getRequest()->getParam('order_id')]);
            }
        } catch (\Exception $e) {
            $this->loggerInterface->critical($e);
            if ($isNeedCreateLabel) {
                $responseAjax->setError(true);
                $responseAjax->setMessage(__('An error occurred while creating shipping label.'));
            } else {
                $this->messageManager->addErrorMessage(__('Cannot save shipment.'));
                return $resultRedirect->setPath('*/*/new', ['order_id' => $this->getRequest()->getParam('order_id')]);
            }
        }
        if ($isNeedCreateLabel) {
            return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setJsonData($responseAjax->toJson());
        }

        return $resultRedirect->setPath('sales/order/view', ['order_id' => $shipment->getOrderId()]);
    }
}
