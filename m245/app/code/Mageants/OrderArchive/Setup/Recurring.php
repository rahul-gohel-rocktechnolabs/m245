<?php
 /**
  * @category Mageants OrderArchive
  * @package Mageants OrderArchive
  * @copyright Copyright (c) 2019 Mageants
  * @author Mageants Team <support@mageants.com>
  */

namespace Mageants\OrderArchive\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mageants\OrderArchive\Model\ResourceModel\Synchronizer;

/**
 * @codeCoverageIgnore
 */
class Recurring implements InstallSchemaInterface
{
    /**
     * Synchronizer instance
     *
     * @var Synchronizer
     */
    protected $synchronizer;

    /**
     * Constructor
     *
     * @param Synchronizer $synchronizer
     */
    public function __construct(Synchronizer $synchronizer)
    {
        $this->synchronizer = $synchronizer;
    }

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->synchronizer->syncArchiveStructure();
    }
}
