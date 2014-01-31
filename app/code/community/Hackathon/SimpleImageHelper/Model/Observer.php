<?php
/**
 * 
 */
class Hackathon_SimpleImageHelper_Model_Observer
{
    public function getImages(Varien_Event_Observer $observer)
    {
        $product = $observer->getData('product');
        
        
    }
}