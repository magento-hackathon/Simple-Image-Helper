<?php

/**
 * Class Hackathon_SimpleImageHelper_Helper_Image
 *
 * @category Hackathon
 * @package  Hackathon_SimpleImageHelper
 * @author   Florinel Chis <florinel.chis@gmail.com>
 * @license  Open Software License (OSL 3.0)
 * @link     http://opensource.org/licenses/osl-3.0.php
 */


class Hackathon_SimpleImageHelper_Helper_Image extends Mage_Core_Helper_Abstract
{


    /**
     * Initialize Helper to work with Image
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $attributeName
     * @param mixed $imageFile
     * @return Mage_Catalog_Helper_Image
     */
    public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile=null)
    {

    }

    /**
     * Do Nothing
     *
     * @see Mage_Catalog_Model_Product_Image
     * @param int $width
     * @param int $height
     * @return Hackathon_SimpleImageHelper_Helper_Data
     */
    public function resize($width, $height = null)
    {
        return $this;
    }

    /**
     * Return Image URL
     *
     * @return string
     */
    public function __toString()
    {

    }
}