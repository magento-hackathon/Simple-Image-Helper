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
    const CONFIG_PATH_ENABLED  = 'catalog/hackathon_simpleimage/enabled';
    const CONFIG_PATH_PREFIX   = 'catalog/hackathon_simpleimage';
    const CONFIG_TYPE_WIDTH    = 'width';
    const CONFIG_TYPE_HEIGHT   = 'height';

    /**
     * media/gallery attributes
     */
    const ATTR_MEDIA        = 'media';
    const ATTR_MEDIA_THUMB  = 'media_thumb';

    /**
     * @var boolean
     */
    protected $_enabled = null;
    /**
     * image configuration cache
     * @var array
     */
    protected $_imageConfig = null;
    /**
     * attribute collection model
     * @var Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    protected $_attributeCollection = null;
    /**
     * check whether image helper is enabled
     * @return boolean
     */
    public function isEnabled()
    {
        if ($this->_enabled === null) {
            $this->_enabled = Mage::getStoreConfigFlag(self::CONFIG_PATH_ENABLED);
        }
        return $this->_enabled;
    }

    /**
     * get configuation for $type for a given $attribute
     * @param str $attribute
     * @param str $suffix
     * @return str|null Returns null if no value found
     */
    public function getImageConfig($attribute, $type)
    {
        if ($this->_imageConfig === null) {
            $config = Mage::getStoreConfig(self::CONFIG_PATH_PREFIX);
            $this->_imageConfig = array();
            foreach ($config as $key => $value) {
                //all image attributes will be prefixed with 'sih'
                if (substr($key, 0,4) == 'sih_') {
                    if (!$value) {//if is falsy convert to null
                        $value = null;
                    }
                    $this->_imageConfig[$key] = $value;
                }
            }
        }
        $key = 'sih_'.$attribute.'_'.$suffix;
        if (isset($this->_imageConfig[$key])) {
            return $this->_imageConfig[$key];
        }
        return null;
    }
    
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
        $backend              = null;
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
                $this->generateProductAssets($product);
            }
            unset($productCollection);
        }
    }

    /**
     * generate product images and update assets attribute value
     * @param Mage_Catalog_Model_Product $product
     */
    public function generateProductAssets(Mage_Catalog_Model_Product $product)
    {
        /* @var $processor Hackathon_SimpleImageHelper_Model_Processor */
        $processor            = Mage::getSingleton('hackathon_simpleimage/processor');
        $paths                = $processor->generateProductImages($product);
        /* @var $actionModel Mage_Catalog_Model_Product_Action */
        $actionModel          = Mage::getSingleton('catalog/product_action');
        $storeCode            = Mage::app()->getStore()->getCode();
        $paths                = $processor->generateProductImages($product);
        $actionModel->updateAttributes(array($product->getId()), array(self::SIMPLEIMAGE_ATTRIBUTE_CODE => $this->jsonEncode($paths)), $storeCode);
    }

    /**
     * get catalog attribute collection filtered by media_image input
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function getMediaAttributeCollection()
    {
        if (!$this->_attributeCollection) {
            /* @var $attributes Mage_Catalog_Model_Resource_Product_Attribute_Collection */
            $attributes = Mage::getResourceModel('catalog/product_attribute_collection');
            $attributes->addFieldToFilter('frontend_input', array('eq' => 'media_image'));
            $this->_attributeCollection = $attributes;
        }
        return $this->_attributeCollection;
    }

    /**
     * @return string
     */
    public function getAttributeCode()
    {
        return self::SIMPLEIMAGE_ATTRIBUTE_CODE;
    }
}