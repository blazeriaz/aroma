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
		$product_id = '11';
		$payment_plan = Mage::getModel('subscription/paymentplan')->load($this->getRequest()->getPost('paymentid_hidden_val'));
		$plan_name = $payment_plan->getName();
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
        $cart->addProduct($product, new Varien_Object($params));
        Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
        $cart->save();
    }
    catch (Exception $ex) {
        echo $ex->getMessage();
    }
	}
}