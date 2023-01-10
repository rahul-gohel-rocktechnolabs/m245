<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Plugin\Ui\Component\Listing;

class Columns
{
    public const DEFAULT_COLUMNS_MAX_ORDER = 100;

    /** @var \Mageants\Orderattribute\Ui\Component\Listing\Attribute\RepositoryInterface */
    protected $attributeRepository;

    /**
     * @var $filterMap
     */
    protected $filterMap = [
        'default' => 'text',
        'select' => 'select',
        'boolean' => 'select',
        'multiselect' => 'select',
        'radios' => 'select',
        'checkboxes' => 'select',
        'date' => 'dateRange',
        'datetime' => 'dateRange',
    ];

    /**
     * @var \Mageants\Orderattribute\Ui\Component\ColumnFactory
     */
    protected $columnFactory;

    /**
     * @var \Mageants\Orderattribute\Helper\Config
     */
    protected $config;

    /**
     * Columns Plugin constructor.
     *
     * @param \Mageants\Orderattribute\Ui\Component\ColumnFactory                         $columnFactory
     * @param \Mageants\Orderattribute\Ui\Component\Listing\Attribute\RepositoryInterface $attributeRepository
     * @param \Mageants\Orderattribute\Helper\Config                                      $config
     */
    public function __construct(
        \Mageants\Orderattribute\Ui\Component\ColumnFactory $columnFactory,
        \Mageants\Orderattribute\Ui\Component\Listing\Attribute\RepositoryInterface $attributeRepository,
        \Mageants\Orderattribute\Helper\Config $config
    ) {
        $this->columnFactory = $columnFactory;
        $this->attributeRepository = $attributeRepository;
        $this->config = $config;
    }

    /**
     * Around Prepare
     *
     * @param \Magento\Ui\Component\Listing\Columns $subject
     * @param \Closure $proceed
     */
    public function aroundPrepare(\Magento\Ui\Component\Listing\Columns $subject, \Closure $proceed)
    {
        if ($this->allowedInlineEdit($subject)) {
            $this->addInlineEdit($subject);
        }
        if ($this->allowToAddAttributes($subject)) {
            $this->prepareOrderAttributes($subject);
        }

        $proceed();
    }

    /**
     * Allowed inline edit
     *
     * @param \Magento\Ui\Component\Listing\Columns $columnsComponent
     * @return bool
     */
    private function allowedInlineEdit($columnsComponent)
    {
        return $columnsComponent->getName() == 'sales_order_columns';
    }

    /**
     * Add inline edit
     *
     * @param \Magento\Ui\Component\Listing\Columns $columnsComponent
     */
    private function addInlineEdit($columnsComponent)
    {
        $config = $columnsComponent->getData('config');
        /* some times xsi:type="boolean" recognizing as string, should be as boolean */
        /** @see app/code/Mageants/Orderattribute/view/adminhtml/ui_component/sales_order_grid.xml */
        $config['childDefaults']['fieldAction'] = [
            'provider' => 'sales_order_grid.sales_order_grid.sales_order_columns_editor',
            'target' => 'startEdit',
            'params' => [
                0 => '${ $.$data.rowIndex }',
                1 => true,
            ],
        ];

        $columnsComponent->setData('config', $config);
    }

    /**
     * Prepare order attributes
     *
     * @param \Magento\Ui\Component\Listing\Columns $columnsComponent
     */
    protected function prepareOrderAttributes($columnsComponent)
    {
        $columnSortOrder = self::DEFAULT_COLUMNS_MAX_ORDER;
        $components = $columnsComponent->getChildComponents();
        foreach ($this->attributeRepository->getList() as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            if (!isset($components[$attributeCode])) {
                $config = [
                    'sortOrder' => ++$columnSortOrder,
                    'add_field' => false,
                    'visible' => true,
                    'filter' => $this->getFilterType($attribute->getFrontendInput()),
                    'editor' => $this->getFilterType($attribute->getFrontendInput()),
                ];
                $column = $this->columnFactory->create($attribute, $columnsComponent->getContext(), $config);
                $column->prepare();
                $columnsComponent->addComponent($attributeCode, $column);
            }
        }
    }

    /**
     * Retrieve filter type by $frontendInput
     *
     * @param string $frontendInput
     * @return string
     */
    protected function getFilterType($frontendInput)
    {
        return isset($this->filterMap[$frontendInput]) ? $this->filterMap[$frontendInput] : $this->filterMap['default'];
    }

    /**
     * Around prepare data source
     *
     * @param \Magento\Ui\Component\Listing\Columns $subject
     * @param \Closure                              $proceed
     * @param array                                 $dataSource
     */
    public function aroundPrepareDataSource(
        \Magento\Ui\Component\Listing\Columns $subject,
        \Closure $proceed,
        array $dataSource
    ) {
        if ($this->allowToAddAttributes($subject)) {
            $dataSource = $this->prepareDataForOrderAttributes($dataSource);
        }

        return $proceed($dataSource);
    }

    /**
     * Prepare data for order attributes
     *
     * @param array $dataSource
     * @return array
     */
    protected function prepareDataForOrderAttributes(array $dataSource)
    {
        $orderAttributesList = $this->attributeRepository->getList();
        foreach ($orderAttributesList as $attribute) {
            /**
             * @var \Magento\Eav\Model\Entity\Attribute $attribute
             */
            if ($attribute->getFrontendInput() == 'checkboxes') {
                $dataSource = $this->prepareDataForCheckboxes(
                    $dataSource,
                    $attribute->getAttributeCode()
                );
            }
        }

        return $dataSource;
    }

    /**
     * Prepare data for checkboxes
     *
     * @param array $dataSource
     * @param string $attributeCode
     * @return array
     */
    protected function prepareDataForCheckboxes(array $dataSource, $attributeCode)
    {
        $items = &$dataSource['data']['items'];
        foreach ($items as &$item) {
            if (array_key_exists($attributeCode, $item) && is_string($item[$attributeCode])) {
                $item[$attributeCode] = explode(',', $item[$attributeCode]);
            }
        }

        return $dataSource;
    }

    /**
     * Is can add order Attribute Columns to Component
     *
     * @param \Magento\Ui\Component\Listing\Columns $columnsComponent
     * @return bool
     */
    public function allowToAddAttributes($columnsComponent)
    {
        $componentName = $columnsComponent->getName();
        $isOrder = $componentName == 'sales_order_columns';
        $isInvoice = $componentName == 'sales_order_invoice_columns' && $this->config->getShowInvoiceGrid();
        $isShipment = $componentName == 'sales_order_shipment_columns' && $this->config->getShowShipmentGrid();
        return $isOrder || $isInvoice || $isShipment;
    }
}
