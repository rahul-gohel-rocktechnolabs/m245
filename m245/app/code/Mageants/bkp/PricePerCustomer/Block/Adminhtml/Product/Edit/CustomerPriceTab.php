<?php
/**
 * @category Mageants PricePerCustomer
 * @package Mageants_PricePerCustomer
 * @copyright Copyright (c) 2020 Mageants
 * @author Mageants Team <support@mageants.com>
 */
 
namespace Mageants\PricePerCustomer\Block\Adminhtml\Product\Edit;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;

/**
 * CustomerPriceTab class for display form and grid in product page
 */
class CustomerPriceTab extends Generic
{
    /**
     * [$_template description]
     * @var string
     */
    protected $_template = 'customerpricetab.phtml';

    /**
     * [$_coreRegistry description]
     * @var null
     */
    protected $_coreRegistry = null;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_systemStore = $systemStore;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * PrepareForm
     *
     * @return Form
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('customerprice_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('')]);
        $fieldset->addField(
            'add_customer_price',
            'note',
            [
                'text' => $this->getButtonHtml(
                    __('Save Customer Price'),
                    "",
                    'add_customer_price'
                ),
                'label' => __('Add Customer Price'),
                'class' => 'add_customer_price',
                'after_element_html' => '<div class="control-value admin__field-value" 
                    style="float: right;padding-right: 5px;">
                    <button class ="action-secondary search_customer" id="action-customer-search"type="button">
                    Search Customer</button></div>'
            ]
        );
        $fieldset->addField(
            'id',
            'hidden',
            [
                'name' => 'id',
                'label' => __('id'),
                'title' => __('id'),
                'class' => 'validate-digits validate-greater-than-zero',
                'after_element_html' => '<div class="customer_error"></div>'
            ]
        );
        $fieldset->addField(
            'customer_id',
            'hidden',
            [
                'name' => 'customer_id',
                'label' => __('Customer id'),
                'title' => __('Customer id'),
                'class' => 'validate-digits validate-greater-than-zero',
                'after_element_html' => '<div class="customer_error"></div>'
            ]
        );
        $fieldset->addField(
            'customer_email',
            'text',
            [
                'name' => 'customer_email',
                'label' => __('Customer Email'),
                'title' => __('Customer Email'),
                'required' => true,
                'class' => 'validate-digits validate-greater-than-zero',
                'disabled' => 'disabled'
            ]
        );
        $fieldset->addField(
            'customer_price',
            'text',
            [
                'name' => 'customer_price',
                'label' => __('Customer Price'),
                'title' => __('Customer Price'),
                'required' => true,
                'class' => 'validate-digits validate-greater-than-zero',
                'note' => 'Examples:<br>±10.99 - increase/decrease current price by given value
                <br>±15% - increase/decrease current price by given percent',
            ]
        );
        $fieldset->addField(
            'special_price',
            'text',
            [
                'name' => 'special_price',
                'label' => __('Customer Special Price'),
                'title' => __('Customer Special Price'),
                'class' => 'validate-digits validate-greater-than-zero',
                'note' => 'If product special price added in product then special price working otherwise special price
                    not increase or decrease.<br>Examples:
                    <br>±10.99 - increase/decrease special price by given value
                    <br>±15% - increase/decrease special price by given percent',
            ]
        );
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Html
     *
     * @return Html
     */
    protected function _toHtml()
    {
        $this->_prepareForm();
        return parent::_toHtml();
    }

    /**
     * Get FormHtml
     *
     * @return Html
     */
    public function getFormHtml()
    {
        $html = parent::getFormHtml();
        $html .= $this->getLayout()->createBlock(
            \Mageants\PricePerCustomer\Block\Adminhtml\Grid\Grid::class
        )->toHtml();
        return $html;
    }

    /**
     * Get SearchCustomerHtml
     *
     * @return Html
     */
    public function getSearchCustomerHtml()
    {
        $html = $this->getLayout()->createBlock(
            \Mageants\PricePerCustomer\Block\Adminhtml\Grid\Searchgrid::class
        )->toHtml();
        return $html;
    }

    /**
     * Get BaseUrl
     *
     * @return BaseUrl
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * Get CurrentProductId
     *
     * @return Id
     */
    public function getCurrentProductID()
    {
        $product = $this->_coreRegistry->registry('current_product');
        return $product->getId();
    }

    /**
     * Get CurrentProductType
     *
     * @return TypeId
     */
    public function getCurrentProductType()
    {
        $product = $this->_coreRegistry->registry('current_product');
        return $product->getTypeId();
    }
}
