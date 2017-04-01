<?php
class Aroma_Subscription_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction(){
		$this->loadLayout();     
		$this->renderLayout();
    }
	public function checkoutAction(){
		//print_r($_POST);
		$cart = Mage::getModel('checkout/cart');
    $cart->init();

/*'options' => array(
            2 => "Payment Schedule",
            3 => "Tier",
            4 => "Frequency",
            5 => "Type",
            6 => "Roast"
        )
		   [roast_hidden_val] => Dark
    [type_hidden_val] => Espresso
    [frequency_hidden_val] => Every 2 weeks
    [tier_hidden_val] => Deluxe
    [paymentid_hidden_val] => 6
		*/
		$default_base_price = Mage::getStoreConfig('subscription/subscription/basic');

		$default_deluxe_price = Mage::getStoreConfig('subscription/subscription/deluxe');
		$product_id = '11';
		$payment_plan = Mage::getModel('subscription/paymentplan')->load($this->getRequest()->getPost('paymentid_hidden_val'));
		$plan_name = $payment_plan->getName();
		$payment_plan->getNoOfShip();
		$payment_plan->getPrice();
		$tier_hd_hidden_val = $this->getRequest()->getPost('tier_hd_hidden_val');
		if($tier_hd_hidden_val == 'base_price_default'){
			$base_price = ($default_base_price * $payment_plan->getPrice())/100;
			$final_base_price = $default_base_price - $base_price;
			$custom_price = $final_base_price * $payment_plan->getNoOfShip();
		}
		if($tier_hd_hidden_val == 'delux_price_default'){
			$deluxe_price = ($default_deluxe_price * $payment_plan->getPrice())/100;
			$final_delux_price = $default_deluxe_price - $deluxe_price;
			$custom_price = $final_delux_price * $payment_plan->getNoOfShip();
		}
		$product = Mage::getModel('catalog/product')->load($product_id);
    $params = array(
        'product' => $product->getId(), 
        'qty' => 1,
        'options' => array(
            2 => $plan_name,
            3 => $this->getRequest()->getPost('tier_hidden_val'),
            4 => $this->getRequest()->getPost('frequency_hidden_val'),
            5 => $this->getRequest()->getPost('type_hidden_val'),
            6 => $this->getRequest()->getPost('roast_hidden_val')
        )
    );      



    try {  
		 $customPrice = Mage::getSingleton('core/session')->setCustomfinalprice($custom_price);
        $cart->addProduct($product, new Varien_Object($params));
        Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
        $cart->save();
    }
    catch (Exception $ex) {
        echo $ex->getMessage();
    }
	}
}