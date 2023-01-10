<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Plugin\Order\Email;

class Attributes
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Mageants\Orderattribute\Helper\Config
     */
    protected $config;

    /**
     * @var \Mageants\Orderattribute\Model\OrderAttributesManagement
     */
    protected $attributesManagement;

    /**
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Mageants\Orderattribute\Model\OrderAttributesManagement $attributesManagement
     * @param \Mageants\Orderattribute\Helper\Config $config
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Mageants\Orderattribute\Model\OrderAttributesManagement $attributesManagement,
        \Mageants\Orderattribute\Helper\Config $config
    ) {
        $this->request = $request;
        $this->attributesManagement = $attributesManagement;
        $this->config = $config;
    }

    /**
     * After to html
     *
     * @param \Magento\Sales\Block\Items\AbstractItems $subject
     * @param string $result
     * @return string
     */
    public function afterToHtml(\Magento\Sales\Block\Items\AbstractItems $subject, $result)
    {
        if ($this->config->getShowEmail()) {
            $attributes = $subject->getChildHtml('order_attributes');
            return $attributes . $result;
        } else {
            return $result;
        }
    }
}
