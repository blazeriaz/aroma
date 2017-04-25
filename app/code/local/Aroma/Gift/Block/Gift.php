<?php
class Aroma_Gift_Block_Gift extends Mage_Core_Block_Template
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

