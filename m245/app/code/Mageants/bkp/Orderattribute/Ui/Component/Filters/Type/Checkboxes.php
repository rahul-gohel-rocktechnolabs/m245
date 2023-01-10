<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Ui\Component\Filters\Type;

class Checkboxes extends \Magento\Ui\Component\Filters\Type\Select
{
    public const NAME = 'filter_checkboxes';

    public const COMPONENT = 'checkboxes';

    /**
     * Apply filter
     *
     * @return void
     */
    protected function applyFilter()
    {
        if (isset($this->filterData[$this->getName()])) {
            $value = sprintf('%%%s%%', $this->filterData[$this->getName()]);
            $conditionType = 'like';

            if (!empty($value) || is_numeric($value)) {
                $filter = $this->filterBuilder->setConditionType($conditionType)
                    ->setField($this->getName())
                    ->setValue($value)
                    ->create();

                $this->getContext()->getDataProvider()->addFilter($filter);
            }
        }
    }
}
