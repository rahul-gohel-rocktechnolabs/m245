<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Ui\Component;

class ColumnFactory extends \Magento\Catalog\Ui\Component\ColumnFactory
{
    /**
     * @var \Mageants\Orderattribute\Block\Data\Form\Element\BooleanFactory
     */
    private $booleanFactory;

    /**
     * @param \Magento\Framework\View\Element\UiComponentFactory $componentFactory
     * @param \Mageants\Orderattribute\Block\Data\Form\Element\BooleanFactory $booleanFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponentFactory $componentFactory,
        \Mageants\Orderattribute\Block\Data\Form\Element\BooleanFactory $booleanFactory
    ) {
        parent::__construct($componentFactory);
        $this->booleanFactory = $booleanFactory;
    }

    /**
     * @var array
     */
    protected $jsComponentMap = [
        'text' => 'Magento_Ui/js/grid/columns/column',
        'select' => 'Magento_Ui/js/grid/columns/select',
        'date' => 'Magento_Ui/js/grid/columns/date',
    ];

    /**
     * @var array
     */
    protected $dataTypeMap = [
        'default' => 'text',
        'text' => 'text',
        'boolean' => 'select',
        'select' => 'select',
        'multiselect' => 'select',
        'radios' => 'select',
        'checkboxes' => 'select',
        'date' => 'date',
    ];

    /**
     * Create
     *
     * @param \Magento\Catalog\Api\Data\ProductAttributeInterface $attribute
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param array $config
     * @return \Magento\Ui\Component\Listing\Columns\ColumnInterface
     */
    public function create($attribute, $context, array $config = [])
    {
        $columnName = $attribute->getAttributeCode();
        $config = array_merge([
            'label' => __($attribute->getDefaultFrontendLabel()),
            'dataType' => $this->getDataType($attribute),
            'add_field' => true,
            'visible' => $attribute->getIsVisibleInGrid(),
            'filter' => ($attribute->getIsFilterableInGrid())
            ? $this->getFilterType($attribute->getFrontendInput())
            : null,
        ], $config);

        if ($attribute->usesSource()) {
            $config['options'] = $attribute->getSource()->getAllOptions();
        }
        if ($attribute->getFrontendInput() == 'boolean') {
            $config['options'] = $this->booleanFactory->create()->getValues();
        }

        $config['component'] = $this->getJsComponent($config['dataType']);

        $arguments = [
            'data' => [
                'config' => $config,
            ],
            'context' => $context,
        ];

        return $this->componentFactory->create($columnName, 'column', $arguments);
    }
}
