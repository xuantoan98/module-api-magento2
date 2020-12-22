<?php

namespace AHT\Setup\Blog;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context )
    {
        $installer = $setup;

        $installer->startSetup();

        if(version_compare($context->getVersion(), '1.2.0', '<')) {
			$installer->getConnection()->addColumn(
				$installer->getTable( 'aht_blog_post' ),
				'test',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'nullable' => true,
					'length' => '12,4',
					'comment' => 'test',
					'after' => 'status'
				]
			);
        }
        
        $installer->endSetup();
    }
}

