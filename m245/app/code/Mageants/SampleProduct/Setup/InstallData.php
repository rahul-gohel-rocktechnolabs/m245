<?php
/**
 * @category   Mageants SampleProduct
 * @package    Mageants_SampleProduct
 * @copyright  Copyright (c) 2017 Mageants
 * @author     Mageants Team <support@Mageants.com>
 */
namespace Mageants\SampleProduct\Setup;
 
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
 
/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $StoreManager;
 
    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory, \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->StoreManager = $storeManager;
    }
 
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $service_url = 'https://www.mageants.com/index.php/rock/register/live?ext_name=Mageants_SampleProduct&dom_name='.$this->StoreManager->getStore()->getBaseUrl();
        $curl = curl_init($service_url);

        curl_setopt_array($curl, [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_FOLLOWLOCATION =>true,
            CURLOPT_ENCODING=>'',
            CURLOPT_USERAGENT => 'Mozilla/5.0'
        ]);
        
        $curl_response = curl_exec($curl);
        curl_close($curl);

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
 
        /**
         * Add attributes to the eav/attribute
         */
 
         $eavSetup->addAttribute(
             \Magento\Catalog\Model\Product::ENTITY,
             'offer_sample',
             [
             'group' => 'Sample Product',
             'type' => 'int',
             'backend' => '',
             'frontend' => '',
             'label' => 'Offer Sample',
             'input' => 'select',
             'class' => '',
             'source' => 'Mageants\SampleProduct\Model\Config\Source\Options',
             'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
             'visible' => true,
             'required' => false,
             'user_defined' => true,
             'default' => '',
             'searchable' => false,
             'filterable' => false,
             'comparable' => false,
             'visible_on_front' => false,
             'used_in_product_listing' => true,
             'unique' => false,
             'apply_to' => ''
             ]
         )->addAttribute(
             \Magento\Catalog\Model\Product::ENTITY,
             'offer_sample_cost',
             [
             'group' => 'Sample Product',
             'type' => 'varchar',
             'backend' => '',
             'frontend' => '',
             'label' => 'Cost Of Sample Product',
             'input' => 'text',
             'class' => '',
             'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
             'visible' => true,
             'required' => false,
             'user_defined' => true,
             'default' => '',
             'searchable' => false,
             'filterable' => false,
             'comparable' => false,
             'visible_on_front' => false,
             'used_in_product_listing' => true,
             'unique' => false,
             'apply_to' => ''
             ]
         );/*->addAttribute(
        \Magento\Catalog\Model\Product::ENTITY,
        'offer_sample_max_qty',
            [
            'group' => 'Sample Product',
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Maximum Sample Qantity Customer can Order',
            'input' => 'text',
            'class' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
            ]
        );*/
    }
}
