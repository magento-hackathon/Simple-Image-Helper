<?php
/**
 * 
 */
class Hackathon_SimpleImageHelper_Model_Observer
{
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