<?php
class Hackathon_SimpleImageHelper_Model_Crontab
{
    /**
     * Cron invoked method to process new images saved via the admin.
     */
    public function process()
    {
        $model = Mage::getModel('simpleimage/simpleimage')->getCollection();

        if ($model) {
            Mage::log('[Simple Image Helper][Info] Processing Image Files');
            foreach ($model as $product) {
                Mage::log('[Simple Image Helper][Debug] Processing Image: ' . $product->getData('product_id'));
                Mage::getModel('simpleimage/processor')->generateGalleryAssets($product->getData('product_id'));

                $imageHelper = Mage::getModel('simpleimage/simpleimage')->load($product->getData('imagehelper_id'));
                $imageHelper->delete();
            }
        } else {
            Mage::log('[Simple Image Helper][Info] No images to process');
        }
    }
}