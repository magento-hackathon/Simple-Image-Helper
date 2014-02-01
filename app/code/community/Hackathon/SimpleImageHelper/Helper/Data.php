<?php

/**
 * Class Hackathon_SimpleImageHelper_Helper_Data
 *
 * @category Hackathon
 * @package  Hackathon_SimpleImageHelper
 * @author   Florinel Chis <florinel.chis@gmail.com>
 * @author   Rolando Granadino <beeplogic@magenation.com>
 * @license  Open Software License (OSL 3.0)
 * @link     http://opensource.org/licenses/osl-3.0.php
 */
class Hackathon_SimpleImageHelper_Helper_Data extends Mage_Core_Helper_Data
{
    const COLLECTION_PAGE_SIZE = 5000;
    const SIMPLEIMAGE_ATTRIBUTE_CODE = 'simpleimage_assets';

    /**
     * loop through all products and generate assets
     */
    public function generateAllProductAssets()
    {
        /* @var $productCollectionPrototype Mage_Catalog_Model_Resource_Product_Collection */
        $productCollectionPrototype = Mage::getResourceModel('catalog/product_collection');
        $productCollectionPrototype->setPageSize(self::COLLECTION_PAGE_SIZE);
        $pageNumbers = $productCollectionPrototype->getLastPageNumber();
        unset($productCollectionPrototype);
        /* @var $processor Hackathon_SimpleImageHelper_Model_Processor */
        $processor            = Mage::getModel('hackathon_simpleimage/processor');
        $backend              = null;
        /* @var $actionModel Mage_Catalog_Model_Product_Action */
        $actionModel          = Mage::getSingleton('catalog/product_action');
        $storeCode            = Mage::app()->getStore()->getCode();
        for ($i = 1; $i <= $pageNumbers; $i++) {
            /* @var $productCollection Mage_Catalog_Model_Resource_Product_Collection */
            $productCollection = Mage::getResourceModel('catalog/product_collection');
            $productCollection->addAttributeToSelect(array('sku', 'image', 'small_image', 'gallery', 'media_gallery', 'thumbnail'));
            $productCollection->setPageSize(self::COLLECTION_PAGE_SIZE);
            $productCollection->setCurPage($i)->load();
            foreach ($productCollection as $product) {
                /* @var $product Mage_Catalog_Model_Product */
                if (!$backend) {
                    $attributes    = $product->getTypeInstance(true)->getSetAttributes($product);
                    $mediaGallery = $attributes['media_gallery'];
                    $backend       = $mediaGallery->getBackend();
                }
                $backend->afterLoad($product);
                $paths = $processor->generateProductImages($product);
                $actionModel->updateAttributes(array($product->getId()), array(self::SIMPLEIMAGE_ATTRIBUTE_CODE => $this->jsonEncode($paths)), $storeCode);
            }
            unset($productCollection);
        }
    }

    /**
     * @return string
     */
    public function getAttributeCode()
    {
        return self::SIMPLEIMAGE_ATTRIBUTE_CODE;
    }
}