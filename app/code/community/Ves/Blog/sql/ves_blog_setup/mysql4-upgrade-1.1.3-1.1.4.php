<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
	ALTER TABLE {$this->getTable('ves_blog/post')} ADD COLUMN `canonical_url` varchar(100);
	ALTER TABLE {$this->getTable('ves_blog/category')} ADD COLUMN `canonical_url` varchar(100);
");

/*

	-- DROP TABLE IF EXISTS `{$this->getTable('ves_blog/post_category')}`;
	CREATE TABLE `{$this->getTable('ves_blog/post_category')}` (
	  `post_id` int(10) unsigned NOT NULL,
	  `category_id` int(10) unsigned NOT NULL,
	  PRIMARY KEY (`post_id`,`category_id`),
	  CONSTRAINT `FK_BLOG_POST_CATEGORY_POST` FOREIGN KEY (`post_id`) REFERENCES `{$this->getTable('ves_blog/post')}` (`post_id`) ON UPDATE CASCADE ON DELETE CASCADE,
	  CONSTRAINT `FK_BLOG_POST_CATEGORY_CATEGORY` FOREIGN KEY (`category_id`) REFERENCES `{$this->getTable('ves_blog/category')}` (`category_id`) ON UPDATE CASCADE ON DELETE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Products categories';

*/

