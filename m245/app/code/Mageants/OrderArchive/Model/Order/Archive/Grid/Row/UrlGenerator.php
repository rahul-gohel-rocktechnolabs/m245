<?php
/**
 * @category Mageants OrderArchive
 * @package Mageants OrderArchive
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\OrderArchive\Model\Order\Archive\Grid\Row;

/**
 * Sales Archive Grid row url generator
 */
class UrlGenerator extends \Magento\Backend\Model\Widget\Grid\Row\UrlGenerator
{
    /**
     * @var $_authorizationModel \Magento\Framework\AuthorizationInterface
     */
    protected $_authorizationModel;

    /**
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param array $args
     */
    public function __construct(
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\AuthorizationInterface $authorization,
        array $args = []
    ) {
        $this->_authorizationModel = $authorization;
        parent::__construct($backendUrl, $args);
    }

    /**
     * Generate row url
     * @param \Magento\Framework\DataObject $item
     * @return string|false
     */
    public function getUrl($item)
    {
        if ($this->_authorizationModel->isAllowed('Mageants_OrderArchive::orders')) {
            return parent::getUrl($item);
        }
        return false;
    }
}
