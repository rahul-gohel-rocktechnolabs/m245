<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Controller\Adminhtml\Attribute;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;

class Edit extends \Mageants\Orderattribute\Controller\Adminhtml\Attribute
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Controller\ResultFactory $resultFactory
    ) {
        parent::__construct($context, $resultPageFactory);
        $this->coreRegistry = $coreRegistry;
        $this->resultFactory = $resultFactory;
    }

    /**
     * Execute
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $id = $this->getRequest()->getParam('attribute_id');
        $model = $this->createEavAttributeModel();

        try {
            if ($id) {
                $model->load($id);

                if (!$model->getId()) {
                    throw new LocalizedException(__('This attribute no longer exists.'));
                } elseif (!$this->isOrderAttribute($model)) {
                    throw new LocalizedException(__('This attribute cannot be edited.'));
                }
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }

        // set entered data if was error when we do save
        $data = $this->_objectManager->get(\Magento\Backend\Model\Session::class)->getAttributeData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $attributeData = $this->getRequest()->getParam('attribute');
        if (!empty($attributeData) && $id === null) {
            $model->addData($attributeData);
        }

        $this->coreRegistry->register('entity_attribute', $model);

        $item = $id ? __('Edit Order Attribute') : __('New Order Attribute');

        $resultPage = $this->createActionPage($item);
        $resultPage->getConfig()->getTitle()->prepend($id ? $model->getName() : __('New Order Attribute'));
        $resultPage->getLayout()
            ->getBlock('attribute_edit_js')
            ->setIsPopup((bool) $this->getRequest()->getParam('popup'));
        return $resultPage;
    }
}
