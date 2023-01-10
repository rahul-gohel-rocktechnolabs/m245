<?php
/**
 * @category Mageants OrderArchive
 * @package Mageants OrderArchive
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\OrderArchive\Model\ResourceModel\Archive;

/**
 * Archive resource factory
 */
class Factory
{
    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param string $className
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     * @throws \InvalidArgumentException
     */
    public function get($className)
    {
        if (!$className) {
            throw new \InvalidArgumentException('Incorrect resource class name');
        }

        return $this->_objectManager->get($className);
    }
}
