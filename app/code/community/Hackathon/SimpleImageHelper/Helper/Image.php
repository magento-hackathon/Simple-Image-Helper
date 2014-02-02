<?php

/**
 * Class Hackathon_SimpleImageHelper_Helper_Image
 *
 * @category Hackathon
 * @package  Hackathon_SimpleImageHelper
 * @author   Florinel Chis <florinel.chis@gmail.com>
 * @author   Rolando Granadino <beeplogic@gmail.com>
 * @license  Open Software License (OSL 3.0)
 * @link     http://opensource.org/licenses/osl-3.0.php
 */
class Hackathon_SimpleImageHelper_Helper_Image extends Mage_Catalog_Helper_Image
{

    /**
     * base data helper
     * @var Hackathon_SimpleImageHelper_Helper_Data
     */
    protected $_dataHelper;
    /**
     * frontend url path
     * @var str
     */
    protected $_frontendPath;

    /**
     * setup core data helper
     */
    public function __construct()
    {
        $this->_dataHelper = Mage::helper('hackathon_simpleimage');
    }

    /**
     * Initialize Helper to work with Image
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $attributeName
     * @param mixed $imageFile
     * @return Mage_Catalog_Helper_Image
     */
    public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile = null)
    {
        $this->_frontendPath = null;
        if ((Mage::app()->getStore()->isAdmin())) {
            parent::init($product, $attributeName, $imageFile);
        } else {
            $assetPaths = $this->_initProductAssets($product);
            if ($assetPaths && isset($assetPaths['site_images'][$attributeName])) {
                $this->_frontendPath =  Mage::getBaseUrl('media').$assetPaths['site_images'][$attributeName]['path'];
            }
            if (!$this->_frontendPath) {//fall back and use placeholder
                parent::init($product, $attributeName, $imageFile);
            }
        }
        return $this;
    }

    /**
     * Return Image URL
     * overload to string method to send frontend path
     * if we've generated one
     * @see Mage_Catalog_Helper_Image::__toString
     * @return string
     */
    public function __toString()
    {
        if ($this->_frontendPath) {
            return $this->_frontendPath;
        }
        return parent::__toString();
    }

    /**
     * unseralize and cache product image asset paths
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function _initProductAssets(Mage_Catalog_Model_Product $product)
    {
        if (!$product->hasData('frontend_simpleimage_assets')) {
            $assetPaths = $this->_dataHelper->jsonDecode($product->getData($this->_dataHelper->getAttributeCode()));
            if (is_array($assetPaths)) {
                $paths = $assetPaths;
            } else {
                $paths = false;
            }
            $product->setData('frontend_simpleimage_assets', $paths);
        }
        return $product->getData('frontend_simpleimage_assets');
    }

    /**
     * resize wrapper method 
     * @see Mage_Catalog_Helper_Image::resize
     */
    public function resize($width, $height)
    {
        if (!$this->_frontendPath) {
            return parent::resize($width);
        }
        return $this;
    }
}