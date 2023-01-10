<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Controller\Adminhtml\Attribute;

class Save extends \Mageants\Orderattribute\Controller\Adminhtml\Attribute
{
    /**
     * @var \Mageants\Orderattribute\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * Save constructor.
     *
     * @param \Magento\Backend\App\Action\Context        $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Mageants\Orderattribute\Helper\Config     $configHelper
     * @param \Magento\Framework\App\ResourceConnection  $resource
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Mageants\Orderattribute\Helper\Config $configHelper,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        parent::__construct($context, $resultPageFactory);
        $this->configHelper = $configHelper;
        $this->resource = $resource;
    }

    /**
     * Execute
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {

        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $redirectBack = $this->getRequest()->getParam('back', false);
            if (isset($data['attribute_code'])
                && in_array($data['attribute_code'], ['quote_id', 'id', 'order_entity_id', 'customer_id', 'create_at'])
            ) {
                $this->messageManager->addErrorMessage(
                    __('You can`t create attribute with this "Attribute Code": %1', $data['attribute_code'])
                );
                $this->_session->setAttributeData($data);
                return $resultRedirect->setPath('*/*/edit', ['_current' => true]);
            }
            $model = $this->createEavAttributeModel();
            $attributeId = $this->getRequest()->getParam('attribute_id', null);

            if ($attributeId) {
                $model->load($attributeId);

                if (!$this->isOrderAttribute($model)) {
                    $this->messageManager->addErrorMessage(__('You can`t update this attribute'));
                    $this->_session->setAttributeData($data);
                    return $resultRedirect->setPath('*/*/edit', ['_current' => true]);
                }

                $data['attribute_code'] = $model->getAttributeCode();
                $data['is_user_defined'] = $model->getIsUserDefined();
                $data['frontend_input'] = $model->getFrontendInput();
                $data['note'] = $model->getNote();
            }

            $data['is_configurable'] = isset($data['is_configurable']) ?
            $data['is_configurable'] : 0;

            if ($model->getIsUserDefined() === null
                || $model->getIsUserDefined() != 0
            ) {
                $data['backend_type']
                = $model->getBackendTypeByInput($data['frontend_input']);
            }

            $defaultValueField = $model->getDefaultValueByInput(
                $data['frontend_input']
            );
            if ($defaultValueField) {
                $data['default_value'] = $this->getRequest()->getParam(
                    $defaultValueField
                );
            } else {
                $data['default_value'] = '';
            }

            if ($data['is_required'] == $this->configHelper->getRequiredOnFrontOnlyId()) {
                $data['required_on_front_only'] = 1;
                $data['is_required'] = 0;
            } else {
                $data['required_on_front_only'] = 0;
            }

            if (!isset($data['apply_to'])) {
                $data['apply_to'] = [];
            }

            $data = $this->setSourceModel($data);

            $data['store_ids'] = '0';

            if ($data['stores']) {
                if (is_array($data['stores'])) {
                    $data['store_ids'] = implode(',', $data['stores']) . ',';
                } else {
                    $data['store_ids'] = $data['stores'] . ',';
                }
                unset($data['stores']);
            }

            if (!empty($data['customer_groups'])) {
                $data['customer_groups'] =
                    implode(',', $data['customer_groups']);
            } else {
                $data['customer_groups'] = '';
            }

            $model->addData($data);

            if (!$attributeId) {
                $model->setEntityTypeId($this->entityTypeId);
                $model->setIsUserDefined(1);
            }

            if ($this->getRequest()->getParam('set')
                && $this->getRequest()->getParam('group')
            ) {

                $model->setAttributeSetId($this->getRequest()->getParam('set'));
                $model->setAttributeGroupId($this->getRequest()->getParam('group'));
            }

            try {
                $model->save();
                $this->saveCustomOrderEavAttribute($model);
                $this->saveShippingMethods($model->getAttributeId());
                if (!$this->getRequest()->getParam('attribute_id')) {

                    $fieldType = $model->getFrontendInput();
                    $attributeValueModel = $this->createAttributeValueModel();
                    $attributeValueModel->addAttributeField(
                        $model->getAttributeCode(),
                        $fieldType
                    );
                }

                if (!$this->getRequest()->getParam('attribute_id', null)
                    || !$this->getRequest()->getParam('id', null)) {
                    $this->messageManager->addSuccess(__('Order attribute was successfully saved.'));
                }
                $this->_session->setAttributeData(false);

                if ($redirectBack) {
                    $resultRedirect->setPath('*/*/edit', [
                        'attribute_id' => $model->getId(),
                        '_current' => true,
                    ]);
                } else {
                    $resultRedirect->setPath('*/*/', []);
                }
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_session->setAttributeData($data);
                return $resultRedirect->setPath(
                    '*/*/edit',
                    ['attribute_id' => $attributeId, '_current' => true]
                );
            }
        }
        return $resultRedirect->setPath('*/*/', []);
    }

    /**
     * Set source model
     *
     * @param object $data
     */
    protected function setSourceModel($data)
    {
        switch ($data['frontend_input']) {
            case 'boolean':
                $data['source_model'] = \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class;
                break;
            case 'multiselect':
            case 'select':
                $data['backend_model'] = \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend::class;
                break;
            case 'checkboxes':
            case 'radios':
                $data['source_model'] = \Magento\Eav\Model\Entity\Attribute\Source\Table::class;
                break;
        }

        return $data;
    }

    /**
     * Create shipping method model
     *
     * @param string $attributeId
     */
    protected function saveShippingMethods($attributeId)
    {
        $shippingMethods = $this->getRequest()->getParam('shipping_methods', []);

        $shippingMethodModel = $this->createShippingMethodModel();
        $shippingMethodModel->saveShippingMethods($attributeId, $shippingMethods);
    }

    /**
     * Create shipping method model
     *
     * @return \Mageants\Orderattribute\Model\ShippingMethod
     */
    protected function createShippingMethodModel()
    {
        return $this->_objectManager->create(\Mageants\Orderattribute\Model\ShippingMethod::class);
    }

    /**
     * Save custom order eav attribute
     *
     * @param object $model
     */
    public function saveCustomOrderEavAttribute($model)
    {
        $postSaveData = [];
        $connection = $this->resource->getConnection();
        $postSaveData['attribute_id'] = $model->getAttributeId();
        $postSaveData['is_visible_on_front'] = $model->getIsVisibleOnFront();
        $postSaveData['is_visible_on_back'] = $model->getIsVisibleOnBack();
        $postSaveData['sorting_order'] = $model->getData('sorting_order');
        $postSaveData['checkout_step'] = $model->getData('checkout_step');
        $postSaveData['is_used_in_grid'] = $model->getData('is_used_in_grid');
        $postSaveData['store_ids'] = $model->getData('store_ids');
        $postSaveData['save_selected'] = $model->getData('save_selected');
        $postSaveData['include_pdf'] = $model->getData('include_pdf');
        $postSaveData['apply_default'] = $model->getData('apply_default');
        $postSaveData['customer_groups'] = $model->getData('customer_groups');
        //$postSaveData['size_text'] = $model->getData('size_text');
        $postSaveData['required_on_front_only'] = $model->getData('required_on_front_only');
        $postSaveData['include_html_print_order'] = $model->getData('include_html_print_order');
        $postSaveData['customer_group_enabled'] = $model->getData('customer_groups');
        $postSaveData['validate_length_count'] = 25;
        $postSaveData['include_api'] = 1;
        $connection->insertOnDuplicate("mageants_orderattribute_order_eav_attribute", $postSaveData);
    }
}
