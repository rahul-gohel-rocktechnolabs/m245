<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model\Config;

class SourceFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Construct
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create Customer Group Source
     *
     * @param string $instanceName
     * @return \Mageants\Orderattribute\Model\Config\Source\CustomerGroup
     */
  
    public function createCustomerGroupSource(
        $instanceName = \Mageants\Orderattribute\Model\Config\Source\CustomerGroup::class
    ) {
        return $this->create($instanceName);
    }
  
    /**
     * Create checkout step source
     *
     * @param string $instanceName
     * @return \Mageants\Orderattribute\Model\Config\Source\CheckoutStep
     */
    public function createCheckoutStepSource(
        $instanceName = \Mageants\Orderattribute\Model\Config\Source\CheckoutStep::class
    ) {
        return $this->create($instanceName);
    }

    /**
     * Get reorder URL
     *
     * @param string $instanceName
     * @return \Magento\Framework\ObjectManagerInterface
     */
    protected function create($instanceName)
    {
        return $this->objectManager->create($instanceName);
    }

    /**
     * Get customer group source
     *
     * @param string $instanceName
     * @return \Mageants\Orderattribute\Model\Config\Source\CustomerGroup
     */
  
    public function getCustomerGroupSource(
        $instanceName = \Mageants\Orderattribute\Model\Config\Source\CustomerGroup::class
    ) {
        return $this->get($instanceName);
    }

    /**
     * Get checkout step source
     *
     * @param string $instanceName
     * @return \Mageants\Orderattribute\Model\Config\Source\CheckoutStep
     */
    public function getCheckoutStepSource(
        $instanceName = \Mageants\Orderattribute\Model\Config\Source\CheckoutStep::class
    ) {
        return $this->get($instanceName);
    }

    /**
     * Get
     *
     * @param string $instanceName
     * @return \Magento\Framework\ObjectManagerInterface
     */
    protected function get($instanceName)
    {
        return $this->objectManager->get($instanceName);
    }
}
