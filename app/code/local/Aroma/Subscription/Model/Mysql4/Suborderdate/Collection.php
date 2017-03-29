<?php
class Aroma_Subscription_Model_Mysql4_Suborderdate_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('subscription/suborderdate');
    }
}