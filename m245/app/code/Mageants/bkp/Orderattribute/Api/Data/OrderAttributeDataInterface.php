<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Api\Data;

interface OrderAttributeDataInterface
{
    /**
     * Set attribute code
     *
     * @param string $code
     * @return $this
     */
    public function setAttributeCode($code);

    /**
     * Get attribute code
     *
     * @return string
     */
    public function getAttributeCode();

    /**
     * Set label
     *
     * @param string $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * Get label
     *
     * @return string|null
     */
    public function getLabel();

    /**
     * Set value
     *
     * @param string|int|array|null $value
     * @return $this
     */
    public function setValue($value);

    /**
     * Set value
     *
     * @return string|int|array|null
     */
    public function getValue();

    /**
     * Set value output
     *
     * @param string|int|null $value
     * @return $this
     */
    public function setValueOutput($value);

    /**
     * Get value output
     *
     * @return string|int|null
     */
    public function getValueOutput();

    /**
     * Set value output admin
     *
     * @since 2.2.0
     * @param string|int|null $value
     *
     * @return $this
     */
    public function setValueOutputAdmin($value);

    /**
     * Set value output admin
     *
     * @since 2.2.0
     * @return string|int|null
     */
    public function getValueOutputAdmin();
}
