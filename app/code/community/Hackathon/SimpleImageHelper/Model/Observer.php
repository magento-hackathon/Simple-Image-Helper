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

    /**
     * check for media gallery changes
     * @param Varien_Event_Observer $event
     */
    public function handleProductSave(Varien_Event_Observer $event)
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = $event->getData('product');
        if ($product instanceof Mage_Catalog_Model_Product) {
            //handle media gallery changes
            $newMedia = $product->getData('media_gallery');
            $oldMedia = $product->getOrigData('media_gallery');
            $newFiles = array();
            $oldFiles = array();

            if (is_array($newMedia)) {
                foreach ($newMedia['images'] as $imageInfo) {
                    $newFiles[] = $imageInfo['file'];
                }
                if (is_array($oldMedia)) {
                    foreach ($oldMedia['images'] as $imageInfo) {
                        $oldFiles[] = $imageInfo['file'];
                    }
                }
            }

            /* @var $helper Hackathon_SimpleImageHelper_Helper_Data */
            $helper           = Mage::helper('hackathon_simpleimage');
            $attributeChanged = false;
            foreach ($helper->getMediaAttributeCollection() as $attribute) {
                if ($product->dataHasChangedFor($attribute->getAttributeCode())) {
                    $attributeChanged = true;
                    break;
                }
            }

            if ($attributeChanged || array_diff($newFiles, $oldFiles)) {
                //@todo this is where we check for queue support or implement our own
                //for proof of concept we'll just call the process manually
                $model = Mage::getModel('simpleimage/simpleimage');
                $model->setData('product_id', $product->getId());
                $model->save();

                //$helper->generateProductAssets($product);
            }
        }
    }
}