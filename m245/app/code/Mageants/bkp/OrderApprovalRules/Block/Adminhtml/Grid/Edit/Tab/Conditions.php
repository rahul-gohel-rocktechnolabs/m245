<?php
/**
 * @category Mageants OrderApprovalRules
 * @package Mageants_OrderApprovalRules
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants <support@mageants.com>
 */

namespace Mageants\OrderApprovalRules\Block\Adminhtml\Grid\Edit\Tab;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\SerializerInterface;

class Conditions extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Ui\Component\Layout\Tabs\TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $_rendererFieldset;

    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $_conditions;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    private $ruleFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset
     * @param \Mageants\OrderApprovalRules\Model\OrderApprovalRulesFactory $orderApprovalRulesFactory
     * @param \Magento\SalesRule\Model\RuleFactory $ruleFactory
     * @param SerializerInterface $serializer
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        \Mageants\OrderApprovalRules\Model\OrderApprovalRulesFactory $orderApprovalRulesFactory,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        SerializerInterface $serializer,
        array $data = []
    ) {
        $this->_rendererFieldset = $rendererFieldset;
        $this->_conditions = $conditions;
        $this->orderApprovalRulesFactory = $orderApprovalRulesFactory;
        $this->serializer = $serializer;
        $this->getRuleFactory = $ruleFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * The getter function to get the new RuleFactory dependency
     *
     * @return \Magento\SalesRule\Model\RuleFactory
     */
    private function getRuleFactory()
    {
        if ($this->ruleFactory === null) {
            $this->ruleFactory = $this->getRuleFactory;
        }
        return $this->ruleFactory;
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function getTabClass()
    {
        return null;
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function getTabUrl()
    {
        return null;
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function getTabTitle()
    {
        return __('Conditions');
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form
     *
     * @return void
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('mageants_orderapprovalrules');
        $rule_id = $this->getRequest()->getParam('id');
        if ($rule_id) {
            $collection = $this->orderApprovalRulesFactory->create()->load($rule_id);
            $formData = $this->serializer->unserialize($collection['custom_rule']);
        }
        if (!$model) {
            $id = $this->getRequest()->getParam('id');
            $model = $this->getRuleFactory()->create();
            $model->load($id);
            
            if (isset($collection)) {
                $setData = $model->getData();
                $setData['conditions_serialized'] = $collection['custom_rule'];
                
                $model->setData($setData);
            }
        }
        $formName = 'edit_form';
        $conditionsFieldSetId = $model->getConditionsFieldSetId($formName);
        $newChildUrl = $this->getUrl(
            'sales_rule/promo_quote/newConditionHtml/form/' . $conditionsFieldSetId,
            ['form_namespace' => $formName]
        );

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $renderer = $this->_rendererFieldset->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            $newChildUrl
        )->setFieldSetId(
            $conditionsFieldSetId
        );

        $fieldset = $form->addFieldset(
            'conditions_fieldset',
            [
                'legend' => __(
                    'Apply the rule only if the following conditions are met (leave blank for all products).'
                )
            ]
        )->setRenderer(
            $renderer
        );

        $fieldset->addField(
            'conditions',
            'text',
            [
                'name'           => 'conditions',
                'label'          => __('Conditions'),
                'title'          => __('Conditions'),
                'required'       => true,
                'data-form-part' => $formName
            ]
        )->setRule(
            $model
        )->setRenderer(
            $this->_conditions
        );

        $form->setValues($model->getData());
        $this->setConditionFormName($model->getConditions(), $formName);
        $this->setForm($form);

        return parent::_prepareForm();
    }
    /**
     * Set the condition form name
     *
     * @param \Magento\Rule\Model\Condition\AbstractCondition $conditions
     * @param [type] $formName
     * @return void
     */
    private function setConditionFormName(\Magento\Rule\Model\Condition\AbstractCondition $conditions, $formName)
    {
        $conditions->setFormName($formName);
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName);
            }
        }
    }
}
