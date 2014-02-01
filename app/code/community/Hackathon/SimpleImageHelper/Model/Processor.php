<?php
class Hackathon_SimpleImageHelper_Model_Processor
{
    /**
     * @var Mage_Catalog_Helper_Image
     */
    protected $_imageHelper = null;
    
    /**
     * temporary until we have our custom helper
     * @var ReflectionMethod
     */
    protected $_reflection  = null;
    
    const ASSET_ATTR         = 'simpleimage_assets';
    
    const CONFIG_SMALL       = 'small_image';
    const CONFIG_BASE        = 'base_image';//@todo use media size?
    const CONFIG_THUMB       = 'thumbnail';
    const CONFIG_MEDIA_THUMB = 'media_thumb';
    const CONFIG_MEDIA       = 'media';
    
    const MEASUREMENT_WIDTH  = 'width';
    const MEASUREMENT_HEIGHT = 'height';
    
    /**
     * setup image helper
     */
    public function __construct()
    {
        $this->_imageHelper = Mage::helper('catalog/image');
        $r = new ReflectionMethod(get_class($this->_imageHelper), '_getModel');
        $r->setAccessible(true);
        $this->_reflection = $r;
    }
    
    /**
     * @todo throw exception if $product->getId() is empty?
     * @param Mage_Catalog_Model_Product|int $product
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct($product)
    {
        if (!$product instanceof Mage_Catalog_Model_Product) {
            $product = Mage::getModel('catalog/product')->load((int)$product);
        }
        return $product;
    }
    
    /**
     * 
     * @param Mage_Catalog_Model_Product|int $product
     */
    public function generateProductImages($product)
    {
        $product = $this->_getProduct($product);
        
        $configs = $this->_getImagePathConfig();
        /* @todo update to our helper? */
        $helper  = $this->_imageHelper;
        
        //generate media images and thumbs
        
        $collection = $this->getProduct()->getMediaGalleryImages();
        
        //generate product listing
        //generate base image
        //generate thumbnail
        
        foreach ($configs as $config) {
            //do we use a fake attribute name to store generated paths
            //in our own directory?
            
//             $image['url'] = $this->getMediaConfig()->getMediaUrl($image['file']);
//             $image['id'] = isset($image['value_id']) ? $image['value_id'] : null;
//             $image['path'] = $this->getMediaConfig()->getMediaPath($image['file']);
//             $images->addItem(new Varien_Object($image));
            
            //$helper->init($product, 'sih', );
        }
        //save values to: simpleimage_data on product
    }
    /**
     * 
     * @param Mage_Catalog_Model_Product|int $product
     * @return array
     */
    public function generateMediaAssets($product)
    {
        //@todo remove this, temporary for testing
        //magento doesn't like cli settings to be -1
        ini_set('memory_limit', '256m');
        $product     = $this->_getProduct($product);
        $collection  = $product->getMediaGalleryImages();
        $mediaWidth  = $this->_getImageTypeConfig(self::CONFIG_MEDIA, self::MEASUREMENT_WIDTH);
        $mediaHeight = $this->_getImageTypeConfig(self::CONFIG_MEDIA, self::MEASUREMENT_HEIGHT);
        $thumbWidth  = $this->_getImageTypeConfig(self::CONFIG_MEDIA_THUMB, self::MEASUREMENT_WIDTH);
        $thumbHeight = $this->_getImageTypeConfig(self::CONFIG_MEDIA_THUMB, self::MEASUREMENT_HEIGHT);
        $paths       = array();
        foreach ($collection as $image) {
            $this->_imageHelper->init($product, self::ASSET_ATTR, $image->getFile())->resize($mediaWidth, $mediaHeight);
            //force generation of image
            (string)$this->_imageHelper;
            //@todo get file path from new helper
           $model = $this->_reflection->invoke($this->_imageHelper);
           $path  = $model->getNewFile();
           $this->_imageHelper->init($product, self::ASSET_ATTR, $image->getFile())->resize($thumbWidth, $thumbHeight);
           //force generation of image
           (string)$this->_imageHelper;
           $model = $this->_reflection->invoke($this->_imageHelper);
           $thumbPath  = $model->getNewFile();
           $paths['path'] = $path;
           $paths['thumb'] = $thumbPath;
        }
        return $paths;
    }
    
    /**
     * @todo pull from config/helper
     * @todo throw exception if type not found or return some sort of default?
     * @return 
     */
    protected function _getImageTypeConfig($type, $measurement)
    {
        $config = array(
            self::CONFIG_SMALL => array(self::MEASUREMENT_WIDTH => 135, self::MEASUREMENT_HEIGHT => null),
            self::CONFIG_BASE  => array(self::MEASUREMENT_WIDTH => 300, self::MEASUREMENT_HEIGHT => null),//reuse media size?
            self::CONFIG_THUMB   => array(self::MEASUREMENT_WIDTH => 75, self::MEASUREMENT_HEIGHT => null),
            self::CONFIG_MEDIA_THUMB => array(self::MEASUREMENT_WIDTH => 66, self::MEASUREMENT_HEIGHT => null),
            self::CONFIG_MEDIA       => array(self::MEASUREMENT_WIDTH => 300, self::MEASUREMENT_HEIGHT => null)
        );
        return $config[$type][$measurement];
    }
}