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

/* @var $attributeSets Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection */
$attributeSets = Mage::getResourceModel('eav/entity_attribute_set_collection');
foreach ($attributeSets as $attributeSet) {
    /* @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
    $this->addAttributeToGroup(Mage_Catalog_Model_Product::ENTITY, $attributeSet->getId(), 'Images', 'simpleimage_assets', 100);
}

$installer->endSetup();
