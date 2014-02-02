<?php
/**
 * @category Hackathon
 * @package  Hackathon_SimpleImageHelper
 * @license  Open Software License (OSL 3.0)
 * @link     http://opensource.org/licenses/osl-3.0.php
 * @author Rolando Granadino <beeplogic@magenation.com>
 */
class Hackathon_SimpleImageHelper_Model_Observer
{
    /**
     * system config init observer
     * dynamically add configuration fields based on
     * media_gallery catalog attributes
     * @param Varien_Event_Observer $event
     */
    public function initSystemConfig(Varien_Event_Observer $event)
    {
        $config = $event->getConfig();
        /* @var $fields Mage_Core_Model_Config_Element */
        $fields = $config->getNode('sections')->catalog->groups->hackathon_simpleimage->fields;
        /* @var $helper Hackathon_SimpleImageHelper_Helper_Data */
        $helper = Mage::helper('hackathon_simpleimage');
        $i      = 1;
        foreach ($helper->getMediaAttributeCollection() as $attribute) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            $base = $fields->addChild('sih_'.$attribute->getAttributeCode().'_width');
            $base->addChild('label', $attribute->getFrontendLabel().' '.$helper->__('Width'));
            $base->addChild('frontend_type', 'text');
            $base->addChild('sort_order', (100 * $i));
            $base->addChild('show_in_default', 1);
            $base->addChild('show_in_website', 0);
            $base->addChild('show_in_store', 0);
            
            $base = $fields->addChild('sih_'.$attribute->getAttributeCode().'_height');
            $base->addChild('label', $attribute->getFrontendLabel().' '.$helper->__('Height'));
            $base->addChild('frontend_type', 'text');
            $base->addChild('sort_order', (101*$i));
            $base->addChild('show_in_default', 1);
            $base->addChild('show_in_website', 0);
            $base->addChild('show_in_store', 0);
            $i++;
        }
    }
    public function getImages(Varien_Event_Observer $observer)
    {
        $product = $observer->getData('product');        
        $images  = $product->getMediaGallery();

        foreach ($images as $image) {
            foreach ($image as $img) {
                // make call here to helper to resize image based on file $img['file];
            }
        }
    }
}