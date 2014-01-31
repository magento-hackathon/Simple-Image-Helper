<?php

/**
 * SimpleImageHelper Setup File
 *
 * @category  magento-hackathon
 * @package   SimpleImageHelper
 * @author    Daniel Niedergesäß <daniel.niedergesaess@gmail.com>
 * @version   1.0.0
 */

/* @var $installer Hackathon_SimpleImageHelper_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$this->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'simpleimage_assets', array(
    'group' => 'General',
    'type' => 'text',
    'backend' => '',
    'frontend' => '',
    'label' => 'Simple Image Assets',
    'input' => 'text',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'searchable' => false,
    'filterable' => true,
    'comparable' => false,
    'visible_on_front' => true,
    'visible_in_advanced_search' => true,
    'used_in_product_listing' => true,
    'unique' => false,
    'apply_to' => 'simple',
));

$installer->endSetup();