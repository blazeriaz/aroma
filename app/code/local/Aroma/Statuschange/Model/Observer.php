<?php
class Aroma_Statuschange_Model_Observer extends Varien_Event_Observer
{
    const XML_PATH_EMAIL_CUSTOMER_RATING_TEMPLATE = 'subscription/newsubscription/rating_template2';    
	public function cusinvoicedStatusChange($event)
    {
        $order = $event->getOrder();
         $orderStatus = $order->getStatus(); 
        if ($order->getStatus() == 'canceled') 
            $this->_sendStatusMail($order);
    }
 
 	
 	public  function _sendStatusMail($order)
    {
		//$collection = Mage::getModel('subscription/suborderdate')->getCollection();
		$collection = Mage::getModel('subscription/subscription')->getCollection();
		$collection->addFieldToFilter("order_id",$order->getIncrementId());
		$collection->addFieldToFilter("status",1);
		if($collection->count() > 0){
		
		$subscribed_order = $collection->getData();
		$subscribed_id = $subscribed_order[0]['id'];
		$subscr_data = Mage::getModel('subscription/subscription')->load($subscribed_id);
		$subscr_data->setStatus(2);
		$subscr_data->setId($subscribed_id)->save(); 
		$sub_dates = Mage::getModel('subscription/suborderdate')->getCollection();
		$sub_dates->addFieldToFilter("eav_sub_id",$subscribed_id);
		$sub_dates->addFieldToFilter("status", array("neq" => 2));

		foreach($sub_dates as $subdate){
			$dates = Mage::getModel('subscription/suborderdate')->load($subscribed_id);
			$dates->setStatus(4);
			$subscr_data->setId($subdate->getId())->save(); 
		}
				
				
		$senderName = Mage::getStoreConfig('trans_email/ident_general/name');
		$senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');
		$sender  = array(
					'name'=> $senderName,
					'email' => $senderEmail
					);
		$order_id = $order->getIncrementId();	


		$emailTemplateVariables = array();
		$emailTemplateVariables['name'] = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
		$emailTemplateVariables['order_id'] = $order->getIncrementId();
		$emailTemplateVariables['store_name'] = $order->getStoreName();
		$emailTemplateVariables['customer_id'] = $order->getCustomerId();
		$emailTemplateVariables['store_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
			
	
				
				$storeId = Mage::app()->getStore()->getId();
				$store_name = Mage::app()->getWebsite()->getName();
				$website_id = Mage::getModel('core/store')->load($order->getStoreId())->getWebsiteId();
				$website = Mage::app()->getWebsite($website_id);
				$website_name = strtolower($website->getName());
				$template = Mage::getStoreConfig(self::XML_PATH_EMAIL_CUSTOMER_RATING_TEMPLATE, $storeId);
				
				$translate = Mage::getSingleton('core/translate');
				$translate->setTranslateInline(false);
				Mage::getModel('core/email_template')
				->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
				->sendTransactional($template,
					$sender,
					$order->getCustomerEmail(),
					$order->getCustomerFirstname(),
					array(
						'sender'  => $senderName,
						'username' => $order->getCustomerFirstname(),
						'order_id' => $order->getIncrementId(),
						'storename' => $website_name,
						'customer_id' => $order->getCustomerId(),
						'order_incr_id' => $order_id,
						
						)
				);
		}
	}
     
}
