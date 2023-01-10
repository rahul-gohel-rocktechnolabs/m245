<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Controller\Adminhtml\Massaction\Attribute;

use Magento\Backend\App\Action;

class Save extends \Mageants\Orderattribute\Controller\Adminhtml\Massaction\Attribute
{
    /**
     * @var \Mageants\Orderattribute\Model\ResourceModel\Order\Attribute\Value
     */
    private $attributeValueModel;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    private $redirectFactory;

    /**
     * @param Action\Context $context
     * @param \Mageants\Orderattribute\Model\ResourceModel\Order\Attribute\ValueFactory $attributeValueModel
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     */
    public function __construct(
        Action\Context $context,
        \Mageants\Orderattribute\Model\ResourceModel\Order\Attribute\ValueFactory $attributeValueModel,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
    ) {
        parent::__construct($context);
        $this->attributeValueModel = $attributeValueModel;
        $this->redirectFactory = $redirectFactory;
    }

    /**
     * Execute
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        if ($data && isset($data['attributes']) && isset($data['order-ids'])) {
            $attributes = $data['attributes'];
            $ids = $data['order-ids'];
            if ($attributes && $ids) {
                $ids = explode(',', $ids);
                try {
                    $this->attributeValueModel->create()->updateAttributes($attributes, $ids);
                    $this->messageManager->addSuccessMessage(__('Order attributes was successfully saved.'));
                    return $this->redirectFactory->create()->setPath('sales/order/index', ['_current' => true]);
                } catch (\Exception $ex) {
                    $this->messageManager->addErrorMessage($ex->getMessage());
                }
            }
        }

        $this->messageManager->addErrorMessage(
            __('Something went wrong while saving the item data.')
        );
        return $this->redirectFactory->create()->setPath('*/*/attribute_edit', ['_current' => true]);
    }
}
