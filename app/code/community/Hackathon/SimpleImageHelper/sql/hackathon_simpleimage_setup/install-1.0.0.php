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
    'label' => 'Simple Image Assets',
    'input' => 'text',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'visible_on_front' => true,
    'used_in_product_listing' => true,
));

$installer->endSetup();
