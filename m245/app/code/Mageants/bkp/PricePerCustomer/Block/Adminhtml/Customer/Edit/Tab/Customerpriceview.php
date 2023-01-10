<?php
/**
 * @category Mageants PricePerCustomer
 * @package Mageants_PricePerCustomer
 * @copyright Copyright (c) 2020 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\PricePerCustomer\Block\Adminhtml\Customer\Edit\Tab;
 
use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;

/**
 * Customer price tab form block
 */
class Customerpriceview extends Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
 
    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Mageants\PricePerCustomer\Model\PricePerCustomerFactory $pricepercustomerfactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Mageants\PricePerCustomer\Model\PricePerCustomerFactory $pricepercustomerfactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_systemStore = $systemStore;
        $this->customerpricefactory = $pricepercustomerfactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    /**
     * Get CustomerId
     *
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(\Magento\Customer\Controller\RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * Get TabLabel
     *
     * @return Customer Price
     */
    public function getTabLabel()
    {
        return __('Customer Price');
    }
    
    /**
     * Get TabTitle
     *
     * @return Customer Price
     */
    public function getTabTitle()
    {
        return __('Customer Price');
    }
    
    /**
     * Can ShowTab
     *
     * @return ShowTab
     */
    public function canShowTab()
    {
        return true;
    }
    
    /**
     * IsHidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }
        return true;
    }
    
    /**
     * Get TabClass
     *
     * @return Class
     */
    public function getTabClass()
    {
        return '';
    }
    
    /**
     * Get TabUrl
     *
     * @return Url
     */
    public function getTabUrl()
    {
        return '';
    }
    
    /**
     * IsAjaxLoaded
     *
     * @return boolean
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * PrepareForm
     *
     * @return Price
     */
    public function _prepareForm()
    {
        if (!$this->canShowTab()) {
            return $this;
        }
        $id = $this->getCustomerId();

        $global_customer_price = '';
        $global_special_price = '';
        $collection = $this->customerpricefactory->create()->getCollection()
                    ->addFieldToFilter('customer_id', $id)
                    ->addFieldToFilter('product_id', 'all')
                    ->getLastItem();
        $price_details = $collection->getData();

        if (!empty($price_details['customer_price'])) {
            $global_customer_price = $price_details['customer_price'];
        }
        if (!empty($price_details['special_price'])) {
            $global_special_price = $price_details['special_price'];
        }

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('myform_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Global Price:')]);
        $profile = $this->getCollectiveProfile();

        $fieldset->addField(
            'customer_price',
            'text',
            [
                'name' => 'global_customer_price',
                'data-form-part' => $this->getData('target_form'),
                'label' => __('Global Customer Price'),
                'title' => __('Global Customer Price'),
                'value' => $global_customer_price,
                'note' => 'This price set the price for all your products for this customer. 
                Examples:<br>±10.99 - increase/decrease current price by given value
                <br>±15% - increase/decrease current price by given percent',
            ]
        );

        $fieldset->addField(
            'special_price',
            'text',
            [
                'name' => 'global_special_price',
                'data-form-part' => $this->getData('target_form'),
                'label' => __('Global Customer Special Price'),
                'title' => __('Global Customer Special Price'),
                'value' => $global_special_price,
                'note' => 'This price set the special price for all your products for this customer. 
                Examples:<br>±10.99 - increase/decrease special price by given value
                <br>±15% - increase/decrease special price by given percent',
            ]
        );
        
        $fieldset->addField(
            'select_product_price',
            'hidden',
            [
                'name' => 'select_product_price',
                'data-form-part' => $this->getData('target_form'),
                'label' => __('product_price'),
                'title' => __('product_price'),
            ]
        );

        $fieldset->addField(
            'product_changes',
            'hidden',
            [
                'name' => 'product_changes',
                'data-form-part' => $this->getData('target_form'),
                'label' => __('product_changes'),
                'title' => __('product_changes'),
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
        if ($this->canShowTab()) {
            $this->_prepareForm();
            return parent::_toHtml();
        } else {
            return '';
        }
    }
    
    /**
     * Get FormHtml
     *
     * @return html
     */
    public function getFormHtml()
    {
        $html = parent::getFormHtml();
        $html .= $this->getLayout()->createBlock(
            \Mageants\PricePerCustomer\Block\Adminhtml\Grid\Customerpricegrid::class
        )->toHtml();
        return $html;
    }
}
