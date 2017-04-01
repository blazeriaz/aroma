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
	
	public function addPostData(Varien_Event_Observer $observer) {
 
		$action = Mage::app()->getFrontController()->getAction();
 
		/// avoid cron jobs interruption
		if (!$action) {
			return;
		}
		
		
 
		if ($action->getFullActionName() == 'restaurant_menu_addtocart') { // add your add to cart action name.
			//Mage::log($action->getRequest()->getParams());
			if ($action->getRequest()->getParam('area_id')) {
	 
				$item = $observer->getProduct();
				
				$city_details = Mage::getModel('improvedaddress/city')->load($action->getRequest()->getParam('area_id'));
				
				$area_name = $city_details->getDefaultName();
				
				$additionalOptions = array();
				
				/// Add here your additional data
				$additionalOptions[] = array('label' => 'Area', 'value' => $area_name);
				
				$item->addCustomOption('additional_options', serialize($additionalOptions));
			}
		}
		
		if($action->getFullActionName() == 'opc_json_checkout'){
			$item = $observer->getProduct();
			
			
			if ($action->getRequest()->getParam('area_id')) {
	 
				$item = $observer->getProduct();
				
				$city_details = Mage::getModel('improvedaddress/city')->load($action->getRequest()->getParam('area_id'));
				
				$area_name = $city_details->getDefaultName();
											

				$additionalOptions = array();
				
				/// Add here your additional data
				$additionalOptions[] = array('label' => 'Area', 'value' => $area_name);
				
				$item->addCustomOption('additional_options', serialize($additionalOptions));
			}
			
		}
	}
	public function updateBranch(Varien_Event_Observer $obs){
		
	}
}
