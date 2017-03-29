<?php

class Aroma_Subscription_Model_Suborderdate extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('subscription/suborderdate');
    }
}
