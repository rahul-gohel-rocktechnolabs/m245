<?php
 /**
  * @category Mageants OrderArchive
  * @package Mageants OrderArchive
  * @copyright Copyright (c) 2019 Mageants
  * @author Mageants Team <support@mageants.com>
  */
namespace Mageants\OrderArchive\Ui\Component;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns;
use Magento\Framework\View\Element\UiComponentInterface;

/**
 * Class MassAction
 */
class MassAction extends \Magento\Ui\Component\MassAction
{
    /**
     * @var \Mageants\OrderArchive\Model\Config $_salesArchiveConfig
     */
    protected $salesArchiveConfig;

    /**
     * @var \Magento\Framework\AuthorizationInterface $_authModel
     */
    protected $authorizationModel;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param \Mageants\OrderArchive\Model\Config $config
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        \Mageants\OrderArchive\Model\Config $config,
        \Magento\Framework\AuthorizationInterface $authorization,
        array $components = [],
        array $data = []
    ) {
        $this->salesArchiveConfig = $config;
        $this->authorizationModel = $authorization;
        parent::__construct($context, $components, $data);
    }

    /**
     * Register component
     *
     * @return void
     */
    public function prepare()
    {
        $config = $this->getData('config');
        if (isset($config['actions'])) {
            if ($this->salesArchiveConfig->isArchiveActive() === false
                || $this->authorizationModel->isAllowed('Mageants_OrderArchive::add') === false
            ) {
                unset($config['actions']['add_order_to_archive']);
                $this->setData('config', $config);
            }
        }
        parent::prepare();
    }
}
