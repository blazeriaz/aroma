<?php
class Aroma_Subscription_Block_Subscription extends Mage_Core_Block_Template
{
   public function getActivPaymentMethods()
	{
	   $payments = Mage::getSingleton('payment/config')->getActiveMethods();
 
	   $methods = array();
 
	   foreach ($payments as $paymentCode=>$paymentModel) {
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
            $methods[$paymentCode] = array(
                'label'   => $paymentTitle,
                'value' => $paymentCode,
            );
        }
 
        return $methods;
 
	}  
}
