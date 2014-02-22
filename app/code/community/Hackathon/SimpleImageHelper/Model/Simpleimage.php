<?php
class Hackathon_SimpleImageHelper_Model_Simpleimage extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        parent::_construct();
        $this->_init('hackathon_simpleimage/simpleimage');
    }
}