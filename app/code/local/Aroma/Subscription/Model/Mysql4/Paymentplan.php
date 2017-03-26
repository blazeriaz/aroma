<?php 
class Aroma_Subscription_Model_Mysql4_Paymentplan extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {	
        $this->_init("subscription/paymentplan","id");
    }
}