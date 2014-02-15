<?php
class Hackathon_SimpleImageHelper_Model_Resource_Simpleimage_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('simpleimage/simpleimage');
    }
}