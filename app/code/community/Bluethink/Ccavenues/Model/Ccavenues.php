<?php
/**************************************************************
 * @Company Name: BlueThink IT Consulting
 * @Author: Bluethink Team
 * @Date: 12 March 2015
 * @Description: Logic implemented for send encrypt data for ccavenue, decrypt data etc.
 * @Email: pramodgupta.bt@gmail.comn
 ***************************************************************/

class Bluethink_Ccavenues_Model_Ccavenues extends Mage_Payment_Model_Method_Abstract
{

	protected $_code = 'ccavenues';
 
	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = false;
	protected $_canUseForMultishipping  = false;
    

    public function _construct()
    {
        parent::_construct();
        $this->_init('ccavenues/ccavenues');
    }


   public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('ccavenues/payment/redirect', array('_secure' => true));
    }

    public function getSubmitUrl(){  

      if(!Mage::getStoreConfig( 'payment/ccavenues/testmode_flag')){
          $url = "https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction";
      }else{
          $url = "https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction";
      }
    return $url ;

    }

   public function getCCavenuesValues(){

     $orderIncrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
     $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
   
    
      $ccavenues['merchant_id'] = Mage::getStoreConfig( 'payment/ccavenues/merchant_id' );

      $ccavenues['order_id'] =  $orderIncrementId ;
      $ccavenues['tid'] =  time() ;
      $ccavenues['currency'] =  $order->getOrderCurrencyCode() ;
      $ccavenues['language'] =  'EN' ;  

    $ccavenues['amount'] = round( $order->base_grand_total, 2 );
    
    $ccavenues['redirect_url'] = Mage::getBaseUrl() . 'ccavenues/payment/response';
    $ccavenues['cancel_url'] = Mage::getBaseUrl() . 'ccavenues/payment/cancel';

    $billingAddress = $order->getBillingAddress();
    $billingData = $billingAddress->getData();

   $shippingAddress = $order->getShippingAddress();
    if ( $shippingAddress )
      $shippingData = $shippingAddress->getData();

    $ccavenues['billing_name'] = $billingData['firstname'] . ' ' . $billingData['lastname'];
    $ccavenues['billing_address'] = $billingAddress->street;
    $ccavenues['billing_city'] = $billingAddress->city;
    $ccavenues['billing_state'] = $billingAddress->region;
    $ccavenues['billing_zip'] =  $billingAddress->postcode;
    $ccavenues['billing_country'] = Mage::getModel( 'directory/country' )->load( $billingAddress->country_id )->getName();
    $ccavenues['billing_tel'] = $billingAddress->telephone;
    $ccavenues['billing_email'] = $order->customer_email;
    

    $ccavenues['delivery_name'] = $shippingData['firstname'] . ' ' . $shippingData['lastname'];
    $ccavenues['delivery_address'] = $shippingAddress->street;
    $ccavenues['delivery_city'] = $shippingAddress->city;
    $ccavenues['delivery_state'] = $shippingAddress->region;
    $ccavenues['delivery_zip'] =$shippingAddress->postcode;
    $ccavenues['delivery_country'] = Mage::getModel( 'directory/country' )->load( $shippingAddress->country_id )->getName();
    $ccavenues['delivery_tel'] = $shippingAddress->telephone;

   
    $merchant_data = '' ;

    foreach ($ccavenues as $key => $value){
      $merchant_data.=$key.'='.urlencode($value).'&';
    }

   // echo $merchant_data ;
   

    $workingKey = Mage::getStoreConfig( 'payment/ccavenues/working_key' );
    $accessCode = Mage::getStoreConfig( 'payment/ccavenues/access_code' );
    
  

    $encrypted_data=$this->encrypt($merchant_data,$workingKey);
    
        $data['encRequest'] = $encrypted_data ;
        $data['access_code'] =  $accessCode ;

        return $data ; 
        
   } 


   private function encrypt($plainText,$key)
  {
    $secretKey = $this->hextobin(md5($key));
    $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
      $openMode = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '','cbc', '');
      $blockSize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
    $plainPad = $this->pkcs5_pad($plainText, $blockSize);
      if (mcrypt_generic_init($openMode, $secretKey, $initVector) != -1) 
    {
          $encryptedText = mcrypt_generic($openMode, $plainPad);
                mcrypt_generic_deinit($openMode);
                
    } 
    return bin2hex($encryptedText);
  }

  public function decrypt($encryptedText,$key)
  {
    $secretKey = $this->hextobin(md5($key));
    $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
    $encryptedText=$this->hextobin($encryptedText);
      $openMode = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '','cbc', '');
    mcrypt_generic_init($openMode, $secretKey, $initVector);
    $decryptedText = mdecrypt_generic($openMode, $encryptedText);
    $decryptedText = rtrim($decryptedText, "\0");
    mcrypt_generic_deinit($openMode);
    return $decryptedText;
    
  }
  //*********** Padding Function *********************

   private function pkcs5_pad ($plainText, $blockSize)
  {
      $pad = $blockSize - (strlen($plainText) % $blockSize);
      return $plainText . str_repeat(chr($pad), $pad);
  }

  //********** Hexadecimal to Binary function for php 4.0 version ********

  private function hextobin($hexString) 
     { 
          $length = strlen($hexString); 
          $binString="";   
          $count=0; 
          while($count<$length) 
          {       
              $subString =substr($hexString,$count,2);           
              $packedString = pack("H*",$subString); 
              if ($count==0)
        {
        $binString=$packedString;
        } 
              
        else 
        {
        $binString.=$packedString;
        } 
              
        $count+=2; 
          } 
            return $binString; 
        } 


}