<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model\Config\Source;

class CustomerGroup implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var \Magento\Customer\Model\Customer\Attribute\Source\Group
     */
    protected $groupSource;

    /**
     * @param \Magento\Customer\Model\Customer\Attribute\Source\Group $groupSource
     */
    public function __construct(\Magento\Customer\Model\Customer\Attribute\Source\Group $groupSource)
    {
        $this->groupSource = $groupSource;
    }

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array_merge(
            [
                [
                    'value' => 0,
                    'label' => __('NOT LOGGED IN'),
                ],
            ],
            $this->groupSource->getAllOptions()
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $optionArray = $this->toOptionArray();
        $labels = array_column($optionArray, 'label');
        $values = array_column($optionArray, 'value');
        return array_combine($values, $labels);
    }
}
