<?php
// @codingStandardsIgnoreFile 

namespace Mageants\OrderApprovalRules\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class Orderapproval implements DataPatchInterface
{
    /**
     * ModuleDataSetupInterface
     *
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    
    /**
     * EavSetupFactory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory          $eavSetupFactory
     */

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }
    public function apply()
    {
        /** @var ModuleDataSetup $eavSetup */
        $this->moduleDataSetup->startSetup();
        $setup=$setup = $this->moduleDataSetup;

        $data[] = ['status' => 'orderapproved', 'label' => 'Order Approved'];
        $data[] = ['status' => 'orderdisapproved', 'label' => 'Order Disapproved'];
        $data[] = ['status' => 'orderapprovalpending', 'label' => 'Order Approval Pending'];
        $setup->getConnection()->insertArray($setup->getTable('sales_order_status'), ['status', 'label'], $data);

        $setup->getConnection()->insertArray(
            $setup->getTable('sales_order_status_state'),
            ['status', 'state', 'is_default','visible_on_front'],
            [
                ['orderapproved','processing', '0', '1'],
                ['orderdisapproved', 'canceled', '0', '1'],
                ['orderapprovalpending', 'pending', '0', '1']
            ]
        );
       
        $this->moduleDataSetup->getConnection()->endSetup();
    }
    public static function getDependencies()
    {
        return [];
    }
    public function getAliases()
    {
        return [];
    }
}
