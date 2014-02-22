<?php
class Hackathon_SimpleImageHelper_Model_Crontab
{
    /**
     * Cron invoked method to process new images saved via the admin.
     */
    public function process()
    {
        $model = Mage::getModel('hackathon_simpleimage/simpleimage')->getCollection();

        foreach ($model as $product) {
            $this->_processImage($product);
        }
    }

    /**
     * Call process model to generate product assets.
     * Once assets are generated delete product from change log table
     *
     * @param $product
     */
    private function _processImage($product)
    {
        Mage::getModel('hackathon_simpleimage/processor')->generateGalleryAssets($product->getData('product_id'));

        try {
            $imageHelper = Mage::getModel('hackathon_simpleimage/simpleimage')->load($product->getData('imagehelper_id'));
            $imageHelper->delete();
        } catch (Exception $e)  {
            Mage::log("[Error] Unable to delete product: $product->getData('product_id') from process table");
        }
    }
}