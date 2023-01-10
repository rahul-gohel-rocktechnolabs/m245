<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Controller\Adminhtml\Order;

class InlineEdit extends \Magento\Backend\App\Action
{
    public const ADMIN_RESOURCE = 'Magento_Sales::sales_order';

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Mageants\Orderattribute\Model\OrderAttributesManagement
     */
    private $attributesManagement;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Mageants\Orderattribute\Model\OrderAttributesManagement $attributesManagement
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Mageants\Orderattribute\Model\OrderAttributesManagement $attributesManagement
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->attributesManagement = $attributesManagement;
        parent::__construct($context);
    }

    /**
     * Execute
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        $postItems = $this->getRequest()->getParam('items', []);

        foreach ($postItems as $orderId => $postData) {
            $this->attributesManagement->saveOrderAttributes($orderId, $postData);
        }

        return $resultJson->setData(
            [
                'messages' => $this->getErrorMessages(),
                'error' => $this->isErrorExists(),
            ]
        );
    }

    /**
     * Get all messages
     *
     * @return array
     */
    private function getErrorMessages()
    {
        $messages = [];
        foreach ($this->getMessageManager()->getMessages()->getItems() as $error) {
            $messages[] = $error->getText();
        }

        return $messages;
    }

    /**
     * Is error exists
     *
     * @return bool
     */
    private function isErrorExists()
    {
        return (bool) $this->getMessageManager()->getMessages(true)->getCount();
    }
}
