<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mageants\Orderattribute\Setup\Patch\Data;

use Mageants\Orderattribute\Model\ResourceModel\Order\Attribute\CollectionFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

class Adddata implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var CollectionFactory
     */
    private $orderAttributeCollectionFactory;

    /**
     * @param CollectionFactory $orderAttributeCollectionFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        CollectionFactory $orderAttributeCollectionFactory,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->orderAttributeCollectionFactory = $orderAttributeCollectionFactory;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $collection = $this->orderAttributeCollectionFactory->create();
        $attributesData = $collection->getData();

        foreach ($attributesData as $attributeData) {
            $sql = sprintf(
                'ALTER TABLE `%s` ADD `%s` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci',
                $setup->getTable('mageants_orderattribute_order_attribute_value'),
                $attributeData['attribute_code'] . '_output'
            );

            $setup->getConnection()->query($sql);
        }

        $this->moduleDataSetup->endSetup();
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getVersion()
    {
        return '1.0.1';
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
