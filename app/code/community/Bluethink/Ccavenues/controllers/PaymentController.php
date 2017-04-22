<?php
/**************************************************************
 * 
 * @Company Name: BlueThink IT Consulting
 * @Author: Bluethink Team
 * @Date: 12 March 2015
 * @Description: Logic implemented for redirect cancel and response
 * @Email: pramodgupta.bt@gmail.comn
 ***************************************************************/
class Bluethink_Ccavenues_PaymentController extends Mage_Core_Controller_Front_Action
{
     
   public function redirectAction(){
   
       	$session = Mage::getSingleton('checkout/session');
	 	    $this->getResponse()->setBody($this->getLayout()->createBlock('ccavenues/redirect')->toHtml());
        $session->unsQuoteId();
        $session->unsRedirectUrl();	 

   }


 

  public function cancelAction(){

     $response = $this->getRequest()->getPost();
     $workingKey= Mage::getStoreConfig( 'payment/ccavenues/working_key' );
     $encResponse=$response['encResp'] ;    //This is the response sent by the CCAvenue Server
     $ccavenuesModel = Mage::getModel('ccavenues/ccavenues');
     $rcvdString=$ccavenuesModel->decrypt($encResponse,$workingKey);    //Crypto Decryption used as per the specified working key.
     $order_status="";
     $decryptValues=explode('&', $rcvdString);
    $dataSize=sizeof($decryptValues);
    for($i = 0; $i < $dataSize; $i++) 
    {
      $information=explode('=',$decryptValues[$i]);
      if($i==3) $order_status=$information[1];
    }

    $orderId = $response['orderNo'];

  if($order_status==="Aborted")
  {
    
    $order = Mage::getModel( 'sales/order' )->loadByIncrementId($orderId);
      if ($order->getId() ) {
        // Flag the order as 'cancelled' and save it
        $order->setState( Mage_Sales_Model_Order::STATE_CANCELED, true, 'Payment failed on ccavenues : Aborted' )->cancel()->save();
        
        }

      $this->_redirect('checkout/onepage/failure');
  }
 }

  public function responseAction(){

    $response = $this->getRequest()->getPost();
     $workingKey= Mage::getStoreConfig( 'payment/ccavenues/working_key' );
     $encResponse=$response['encResp'] ;    //This is the response sent by the CCAvenue Server
     $ccavenuesModel = Mage::getModel('ccavenues/ccavenues');
     $rcvdString=$ccavenuesModel->decrypt($encResponse,$workingKey);    //Crypto Decryption used as per the specified working key.
     $order_status="";
     $decryptValues=explode('&', $rcvdString);
    $dataSize=sizeof($decryptValues);
    for($i = 0; $i < $dataSize; $i++) 
    {
      $information=explode('=',$decryptValues[$i]);
      if($i==3) $order_status=$information[1];
    }

    $orderId = $response['orderNo'];

  if($order_status==="Success")
  {
    
    $order = Mage::getModel( 'sales/order' )->loadByIncrementId($orderId);
      if ($order->getId() ) {
        // Flag the order as 'cancelled' and save it
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Payment successfull on ccavenues.')->save();
        $order->sendNewOrderEmail();
      }
    $this->_redirect('checkout/onepage/success');

  }

  if($order_status==="Failure")
  {
    
    $order = Mage::getModel( 'sales/order' )->loadByIncrementId($orderId);
      if ($order->getId() ) {
        // Flag the order as 'cancelled' and save it
        $order->cancel()->setState( Mage_Sales_Model_Order::STATE_CANCELED, true, 'Payment failed on ccavenues : Failure ' )->save();
       
      }
    $this->_redirect('checkout/onepage/failure');

  }

  }



}