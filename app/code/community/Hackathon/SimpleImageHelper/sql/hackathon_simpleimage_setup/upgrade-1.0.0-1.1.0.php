<?php
/**
* SimpleImageHelper Setup File
*
* @category  magento-hackathon
* @package   SimpleImageHelper
* @author    James Cowie <james@jcowie.co.uk>
* @version   1.1.0
*/

/* @var $this Mage_Catalog_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('hackathon_simpleimage')};
CREATE TABLE {$this->getTable('hackathon_simpleimage')} (
  `imagehelper_id` int(11) unsigned NOT NULL auto_increment,
  `product_id` int(11) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`imagehelper_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->endSetup();
