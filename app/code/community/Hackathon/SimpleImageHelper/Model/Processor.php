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

    /**
     * @var Hackathon_SimpleImageHelper_Helper_Data
     */
    protected $_helper  = null;

    /**
     * setup image helper
     */
    public function __construct()
    {
        $this->_imageHelper = Mage::helper('catalog/image');
        $this->_helper      = Mage::helper('hackathon_simpleimage');
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
     * @param Mage_Catalog_Model_Product|int $product
     */
    public function generateProductImages($product)
    {
        $product = $this->_getProduct($product);
        $helper  = $this->_imageHelper;
        //generate media images and thumbs
        $galleryPaths = $this->generateGalleryAssets($product);
        $return       = array(
            'gallery'     => $galleryPaths,
            'site_images' => array()
        );
        $attributes = $this->_helper->getMediaAttributeCollection();
        foreach ($attributes as $attribute) {
            $code = $attribute->getAttributeCode();
            $return['site_images'][$code] = $this->generateProductAttributeImage($product, $code);
        }
        return $return;
    }

    /**
     * @param Mage_Catalog_Model_Product|int $product
     * @return array
     */
    public function generateGalleryAssets($product)
    {
        $product     = $this->_getProduct($product);
        $collection  = $product->getMediaGalleryImages();
        
        $mediaWidth   = $this->_helper->getImageConfig(Hackathon_SimpleImageHelper_Helper_Data::ATTR_MEDIA, Hackathon_SimpleImageHelper_Helper_Data::CONFIG_TYPE_WIDTH);
        $mediaHeight  = $this->_helper->getImageConfig(Hackathon_SimpleImageHelper_Helper_Data::ATTR_MEDIA, Hackathon_SimpleImageHelper_Helper_Data::CONFIG_TYPE_HEIGHT);
        $thumbWidth   = $this->_helper->getImageConfig(Hackathon_SimpleImageHelper_Helper_Data::ATTR_MEDIA_THUMB, Hackathon_SimpleImageHelper_Helper_Data::CONFIG_TYPE_WIDTH);
        $thumbHeight  = $this->_helper->getImageConfig(Hackathon_SimpleImageHelper_Helper_Data::ATTR_MEDIA_THUMB, Hackathon_SimpleImageHelper_Helper_Data::CONFIG_TYPE_HEIGHT);
        $paths        = array();
        $mediaUrl     = Mage::getBaseUrl('media');
        foreach ($collection as $image) {
            $this->_imageHelper->init($product, $this->_helper->getAttributeCode(), $image->getFile())->resize($mediaWidth, $mediaHeight);
            //force generation of image
           $url  = (string)$this->_imageHelper;
           $path = str_replace($mediaUrl, '', $url);
           $this->_imageHelper->init($product, $this->_helper->getAttributeCode(), $image->getFile())->resize($thumbWidth, $thumbHeight);
           //force generation of image
           $url        = (string)$this->_imageHelper;
           $thumbPath  = str_replace($mediaUrl, '', $url);
           $paths[] = array('path' => $path, 'thumb' => $thumbPath, 'orig' => $image->getFile());
        }
        return $paths;
    }
    
    /**
     * generate product image for a given attribute
     * @param Mage_Catalog_Model_Product|int $product
     * @param str $attribute
     * @param str $type
     */
    public function generateProductAttributeImage($product, $attribute)
    {
        $product = $this->_getProduct($product);
        $mediaWidth  = $this->_helper->getImageConfig($attribute, Hackathon_SimpleImageHelper_Helper_Data::CONFIG_TYPE_WIDTH);
        $mediaHeight = $this->_helper->getImageConfig($attribute, Hackathon_SimpleImageHelper_Helper_Data::CONFIG_TYPE_HEIGHT);
        
        $this->_imageHelper->init($product, $this->_helper->getAttributeCode(), $product->getData($attribute))->resize($mediaWidth, $mediaHeight);
        //force generation of image
        $url = (string)$this->_imageHelper;
        return array('path'=> str_replace(Mage::getBaseUrl('media'), '', $url), 'orig' => $product->getData($attribute));
    }
}