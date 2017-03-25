<?php
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();
$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('ves_parallax/banner')}`;
CREATE TABLE `{$this->getTable('ves_parallax/banner')}` (
  `banner_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `position` varchar(200) DEFAULT NULL,
  `percent` varchar(25) DEFAULT NULL,
  `scroll` float(10,3) DEFAULT NULL,
  `params` text DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `image_width` varchar(30) DEFAULT 'auto',
  `image_height` varchar(30) DEFAULT 'auto',
  `created_at` date DEFAULT '0000-00-00',
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`banner_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Ves Parallax';

-- DROP TABLE IF EXISTS `{$this->getTable('ves_parallax/banner_store')}`;
CREATE TABLE `{$this->getTable('ves_parallax/banner_store')}` (
  `banner_id` int(10) unsigned NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`banner_id`,`store_id`),
  CONSTRAINT `FK_PARALLAX_BANNER_BANNER_STORE_THEME` FOREIGN KEY (`banner_id`) REFERENCES `{$this->getTable('ves_parallax/banner')}` (`banner_id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `FK_PARALLAX_BANNER_BANNER_STORE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Banner items to Stores';

");
$installer->endSetup();

