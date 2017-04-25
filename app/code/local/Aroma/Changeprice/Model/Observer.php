<?php
class Aroma_Changeprice_Model_Observer{
    public function updatePrice(Varien_Event_Observer $obs)
            {   
				$p = $obs->getQuoteItem(); 
				$customPrice = Mage::getSingleton('core/session')->getCustomfinalprice();
				
				if($customPrice){
					$p->setCustomPrice($customPrice)->setOriginalCustomPrice($customPrice);
					$p->setIsPriceCustom(1);
					Mage::getSingleton('core/session')->unsCustomfinalprice();
				}
				
				
            }
	
}
