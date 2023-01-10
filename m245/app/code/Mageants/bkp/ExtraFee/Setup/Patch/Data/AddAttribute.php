<?php

namespace Mageants\ExtraFee\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Serialize\Serializer\Json;

class AddAttribute implements DataPatchInterface
{
   /** @var ModuleDataSetupInterface */
    private $moduleDataSetup;

   /** @var EavSetupFactory */
    private $eavSetupFactory;

   /**
    * @param ModuleDataSetupInterface $moduleDataSetup
    * @param EavSetupFactory $eavSetupFactory
    * @param StateInterface $inlineTranslation
    * @param Json $serializer
    */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        StateInterface $inlineTranslation,
        Json $serializer = null
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
    }

   /**
    * @inheritdoc
    */
    public function apply()
    {
       /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'override_cat');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'override_cat',
            [
            'group' => 'Mageants ExtraFee',
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Override ExtraFee category',
            'input' => 'select',
            'class' => '',
            'source' => \Mageants\ExtraFee\Model\ExtraFee\Source\OverrideCat::class,
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => 'No',
            'sort_order' => 50,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => 'simple,configurable,virtual,bundle,downloadable'
            ]
        );

        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'extrafeelist');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'extrafeelist',
            [
                'group' => 'Mageants ExtraFee',
                'type' => 'text',
                'backend' => \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend::class,
                'frontend' => '',
                'label' => 'Select ExtraFeeList',
                'input' => 'multiselect',
                'class' => '',
                'source' => \Mageants\ExtraFee\Model\ExtraFee\Source\ExtraFeeList::class,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'sort_order' => 51,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false
            ]
        );
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
    public function getAliases()
    {
        return [];
    }
}
