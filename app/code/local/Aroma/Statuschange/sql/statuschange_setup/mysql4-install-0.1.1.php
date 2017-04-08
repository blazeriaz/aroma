<?php
 /*
$installer = $this;
$installer->startSetup();
 
$installer->getConnection()
    ->addColumn($installer->getTable('sales/order'),
        'review_mailsent',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'length' => 1,
			'after'     => 'status',
            'nullable' => false,
            'default' => 0,
            'comment' => 'Send Review Mail After Placing Order'
        )
    );
 
$installer->endSetup();*/