<?php
class Aroma_Subscription_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction(){
		$this->loadLayout();     
		$this->renderLayout();
    }
	public function checkoutAction(){
	
	try{
		
		$data = Mage::app()->getRequest()->getPost();
		//echo "<pre>";print_r($data);die;
        $customer = Mage::getModel('customer/customer');
        $password = $data['billing']['customer_password'];
		if(!Mage::getSingleton('customer/session')->isLoggedIn()){
			$email = $data['billing']['email'];
		}
		else {
			$customer_data=Mage::getSingleton('customer/session')->getCustomer();
			$email = $customer_data->getEmail(); 	
		}		
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
        $customer->loadByEmail($email);
        if(!$customer->getId()) {
            $customer->setEmail($email);
            $customer->setFirstname($data['billing']['firstname']);
            $customer->setLastname($data['billing']['lastname']);
            $customer->setPassword($password);
			$customer->save();
            $customer->setConfirmation(null);
            $customer->save();
            Mage::getSingleton('customer/session')->loginById($customer->getId());
        }
         
      
		
	$quote = Mage::getModel('sales/quote')->setStoreId(Mage::app()->getStore('default')->getId());

	$customer = Mage::getModel('customer/customer')
						->setWebsiteId(1)
						->loadByEmail($email);
	$quote->assignCustomer($customer);
	
	$quote->setCustomerEmail($email);

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
		$customPrice = Mage::getSingleton('core/session')->setCustomfinalprice($custom_price);
		$product = Mage::getModel('catalog/product')->load($product_id);
    $buyInfo = array(
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
	
	
	$quote->addProduct($product, new Varien_Object($buyInfo));

	
	
	$addressData = array (
            'firstname' => $data['billing']['firstname'],
            'lastname' => $data['billing']['lastname'],
            'street' => array (
                '0' => $data['billing']['street'][0],
                '1' => $data['billing']['street'][1],
            ),
         
            'city' => $data['billing']['city'],
            'region_id' => $data['billing']['region_id'],
            'region' => $data['billing']['region'],
            'postcode' => $data['billing']['postcode'],
            'country_id' => $data['billing']['country_id'], /* Croatia */
            'telephone' => $data['billing']['telephone'],
        );
	$billing_address_id = $this->getRequest()->getPost('billing_address_id'); 
	if($billing_address_id == "") {
		$customAddress = Mage::getModel('customer/address');
		$customAddress->setData($addressData)
					->setCustomerId($customer->getId())
					->setIsDefaultBilling('1')
					->setIsDefaultShipping('1')
					->setSaveInAddressBook($data['billing']['save_in_address_book']);
		if($data['billing']['save_in_address_book'] != "")			
				$customAddress->save();
	}
	$billingAddress = $quote->getBillingAddress()->addData($addressData);
	$shippingAddress = $quote->getShippingAddress()->addData($addressData);

	$shippingAddress->setCollectShippingRates(true)->collectShippingRates()
                ->setShippingMethod('flatrate_flatrate')
                ->setPaymentMethod($data['payment_method']);


// For coupon applied on this order             

//$quote->setCouponCode('test')->setDiscountAmount('10')->setBaseDiscountAmount('10')->setDiscountDescription('your-discount-description');

$quote->getPayment()->importData(array('method' => $data['payment_method']));
$quote->collectTotals()->save();

$service = Mage::getModel('sales/service_quote', $quote);
$service->submitAll();
$order = $service->getOrder();

printf("Created order %s\n", $order->getIncrementId());

exit;
	}catch(Exception $e){
		echo $e->getMessage();
		exit;
	}
		

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
		

	}
	
	public function coffeesubscriptionAction(){
		
		$data  = $this->getRequest()->getPost();
		
		$subscription_selected = array(
									'roast_level'=>$data['roast_level'],
									'coffee_type'=>$data['coffee_type'],
									'how_often'=>$data['how_often'],
									'tier_type'=>$data['tier_type'],
									'total_delux_price'=>$data['total_delux_price'],
									'payment_plan'=>$data['payment_plan'],
									'roast_hidden_val'=>$data['roast_hidden_val'],
									'type_hidden_val'=>$data['type_hidden_val'],
									'frequency_hidden_val'=>$data['frequency_hidden_val'],
									'tier_hidden_val'=>$data['tier_hidden_val'],
									'paymentid_hidden_val'=>$data['paymentid_hidden_val'],
									'tier_hd_hidden_val'=>$data['tier_hd_hidden_val'],
									
									);
		$session = Mage::getSingleton('core/session');
		$session->setData('subscription_request', $subscription_selected);
	}
}