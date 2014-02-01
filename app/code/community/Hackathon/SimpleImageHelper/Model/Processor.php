<?php
/**
 * @category Hackathon
 * @package  Hackathon_SimpleImageHelper
 * @author   Florinel Chis <florinel.chis@gmail.com>
 * @license  Open Software License (OSL 3.0)
 * @link     http://opensource.org/licenses/osl-3.0.php
 * @author Rolando Granadino <beeplogic@magenation.com>
 */
class Hackathon_SimpleImageHelper_Model_Processor
{
    /**
     * @var Mage_Catalog_Helper_Image
     */
    protected $_imageHelper = null;
    
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
        //magento doesn't like cli settings to be -1
        //@todo remove when ready
        ini_set('memory_limit', '256m');
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
        /* @todo update to our helper? */
        $helper  = $this->_imageHelper;
        //generate media images and thumbs
        $galleryPaths = $this->generateGalleryAssets($product);
        //generate product listing
        $smallImage   = $this->generateProductListing($product);
        //generate base image
        $baseImage    = $this->generateProductBaseImage($product);
        //generate thumbnail
        $thumbnail    = $this->generateProductThumbnail($product);
        return array(
            'gallery'     => $galleryPaths,
            'site_images' => array(
                'thumbnail'   => $thumbnail,
                'small_image' => $smallImage,
                'base_image'  => $baseImage
            )
        );
    }
    /**
     * 
     * @param Mage_Catalog_Model_Product|int $product
     * @return array
     */
    public function generateGalleryAssets($product)
    {
        //@todo remove this, temporary for testing
        $product     = $this->_getProduct($product);
        $collection  = $product->getMediaGalleryImages();
        $mediaWidth  = $this->_getImageTypeConfig(self::CONFIG_MEDIA, self::MEASUREMENT_WIDTH);
        $mediaHeight = $this->_getImageTypeConfig(self::CONFIG_MEDIA, self::MEASUREMENT_HEIGHT);
        $thumbWidth  = $this->_getImageTypeConfig(self::CONFIG_MEDIA_THUMB, self::MEASUREMENT_WIDTH);
        $thumbHeight = $this->_getImageTypeConfig(self::CONFIG_MEDIA_THUMB, self::MEASUREMENT_HEIGHT);
        $paths       = array();
        $mediaUrl    = Mage::getBaseUrl('media');
        foreach ($collection as $image) {
            $this->_imageHelper->init($product, self::ASSET_ATTR, $image->getFile())->resize($mediaWidth, $mediaHeight);
            //force generation of image
           $url  = (string)$this->_imageHelper;
           $path = str_replace($mediaUrl, '', $url);
           $this->_imageHelper->init($product, self::ASSET_ATTR, $image->getFile())->resize($thumbWidth, $thumbHeight);
           //force generation of image
           $url        = (string)$this->_imageHelper;
           $thumbPath  = str_replace($mediaUrl, '', $url);
           $paths[] = array('path' => $path, 'thumb' => $thumbPath, 'orig' => $image->getFile());
        }
        return $paths;
    }
    
    /**
     * generate product list image
     * @param Mage_Catalog_Model_Product|int $product $product
     * @return str
     */
    public function generateProductListing($product)
    {
        return $this->generateProductAttributeImage($product, 'small_image', self::CONFIG_SMALL);
    }
    
    /**
     * generate product thumbnail image
     * @param Mage_Catalog_Model_Product|int $product $product
     * @return str
     */
    public function generateProductThumbnail($product)
    {
        return $this->generateProductAttributeImage($product, 'thumbnail', self::CONFIG_THUMB);
    }
    
    /**
     * generate product base image
     * @param Mage_Catalog_Model_Product|int $product $product
     * @return str
     */
    public function generateProductBaseImage($product)
    {
        return $this->generateProductAttributeImage($product, 'image', self::CONFIG_BASE);
    }
    
    /**
     * generate product image for a given attribute
     * @param Mage_Catalog_Model_Product|int $product
     * @param str $attribute
     * @param str $type
     */
    public function generateProductAttributeImage($product, $attribute, $type)
    {
        $product = $this->_getProduct($product);
        $mediaWidth  = $this->_getImageTypeConfig($type, self::MEASUREMENT_WIDTH);
        $mediaHeight = $this->_getImageTypeConfig($type, self::MEASUREMENT_HEIGHT);
        $this->_imageHelper->init($product, self::ASSET_ATTR, $product->getData($attribute))->resize($mediaWidth, $mediaHeight);
        //force generation of image
        $url = (string)$this->_imageHelper;
        return array('path'=> str_replace(Mage::getBaseUrl('media'), '', $url), 'orig' => $product->getData($attribute));
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