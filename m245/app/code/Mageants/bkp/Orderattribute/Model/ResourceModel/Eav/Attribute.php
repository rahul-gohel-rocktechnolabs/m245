<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model\ResourceModel\Eav;

use Mageants\Orderattribute\Api\Data\OrderAttributeInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Eav\Model\Entity\Attribute as EntityAttribute;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;

class Attribute extends EntityAttribute implements OrderAttributeInterface, ScopedAttributeInterface
{
    public const MODULE_NAME = 'Mageants_Orderattribute';

    public const ENTITY = 'mageants_orderattribute_order_eav_attribute';

    /**
     * @var \Mageants\Orderattribute\Model\Order\Attribute\Value
     */
    protected $attributeValue;

    /**
     * @var $customerSession
     */
    private $customerSession;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Eav\Model\Entity\TypeFactory $eavTypeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Eav\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $optionDataFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Catalog\Model\Product\ReservedAttributeList $reservedAttributeList
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param DateTimeFormatterInterface $dateTimeFormatter
     * @param CustomerSession $customerSession
     * @param \Mageants\Orderattribute\Model\Order\Attribute\Value $attributeValue
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\Entity\TypeFactory $eavTypeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $optionDataFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Catalog\Model\Product\ReservedAttributeList $reservedAttributeList,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        DateTimeFormatterInterface $dateTimeFormatter,
        CustomerSession $customerSession,
        \Mageants\Orderattribute\Model\Order\Attribute\Value $attributeValue,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $eavConfig,
            $eavTypeFactory,
            $storeManager,
            $resourceHelper,
            $universalFactory,
            $optionDataFactory,
            $dataObjectProcessor,
            $dataObjectHelper,
            $localeDate,
            $reservedAttributeList,
            $localeResolver,
            $dateTimeFormatter,
            $resource,
            $resourceCollection,
            $data
        );
        $this->attributeValue = $attributeValue;
        $this->customerSession = $customerSession;
    }

    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init(\Mageants\Orderattribute\Model\ResourceModel\Attribute::class);
    }

    /**
     * @var $_eventPrefix
     */
    protected $_eventPrefix = 'mageants_orderattribute_order_eav_attribute';

    /**
     * Get default attribute source model
     *
     * @return object
     */
    public function _getDefaultSourceModel()
    {
        return \Magento\Eav\Model\Entity\Attribute\Source\Table::class;
    }

    /**
     * Get is front required
     *
     * @return object
     */
    public function getIsFrontRequired()
    {
        return ($this->getIsRequired() || $this->getRequiredOnFrontOnly()) ? 1 : 0;
    }

    /**
     * Get default or last value
     *
     * @return string
     */
    public function getDefaultOrLastValue()
    {
        $value = ($this->getSaveSelected() && $this->getLastValue())
        ? $this->getLastValue()
        : $this->getDefaultValue();

        return $value;
    }

    /**
     * Get last value model
     *
     * @return \Mageants\Orderattribute\Model\Order\Attribute\Value
     */
    protected function getLastValueModel()
    {
        if ($this->getData('last_order_value')) {
            return $this->getData('last_order_value');
        }

        $customerId = $this->customerSession->getCustomerId();
        $attributeValue = $this->attributeValue->getLastValueByCustomerId(
            $customerId
        );

        $this->setData('last_order_value', $attributeValue);

        return $attributeValue;
    }

    /**
     * Get last value
     *
     * @return \Mageants\Orderattribute\Model\Order\Attribute\Value
     */
    protected function getLastValue()
    {

        $attributeValue = $this->getLastValueModel();

        $lastValue = $attributeValue->getId()
        ? $attributeValue->getData($this->getAttributeCode())
        : null;

        return $lastValue;
    }

    /**
     * Load order attribute by code
     *
     * @param string $attributeCode
     * @return \Mageants\Orderattribute\Model\ResourceModel\Eav\Attribute
     */
    public function loadOrderAttributeByCode($attributeCode)
    {
        return $this->_eavConfig->getAttribute(
            \Magento\Sales\Model\Order::ENTITY,
            $attributeCode
        );
    }

    /**
     * Get order attributes codes
     *
     * @return array
     */
    public function getOrderAttributesCodes()
    {
        return $this->_eavConfig->getEntityAttributeCodes(
            \Magento\Sales\Model\Order::ENTITY
        );
    }

    /**
     * Uses Source
     *
     * @return string
     */
    public function usesSource()
    {
        $input = $this->getFrontendInput();
        return parent::usesSource() || ($input === 'radios' || $input === 'checkboxes');
    }

    /**
     * Is order attribute
     *
     * @param  string $attributeCode
     * @return bool
     */
    public function isOrderAttribute($attributeCode)
    {
        $orderAttributeCodes = $this->getOrderAttributesCodes();
        return in_array($attributeCode, $orderAttributeCodes);
    }

    /**
     * Is scope global
     *
     * @return bool
     */
    public function isScopeGlobal()
    {
        return true;
    }

    /**
     * Is scope website
     *
     * @return bool
     */
    public function isScopeWebsite()
    {
        return false;
    }
}
