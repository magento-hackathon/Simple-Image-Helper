<?php
class Hackathon_SimpleImageHelper_Model_Mysql4_Simpleimage extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('simpleimage/simpleimage', 'imagehelper_id');
    }
}