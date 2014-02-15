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

$table = $installer->getConnection()->newTable($installer->getTable('hackathon_simpleimage'))
    ->addColumn('imagehelper_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
        'identity' => true,
    ), 'Image Helper ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ), 'Product ID')
    ->addColumn('created_time', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    ), 'Created Date')
    ->addColumn('update_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Updated Time')
    ->setComment('Magento Hackaton simple image helper CL table');
$installer->getConnection()->createTable($table);


$installer->endSetup();
