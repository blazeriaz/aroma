<?php
 
class Aroma_Subscription_Adminhtml_ListController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('subscription/index')
            ->_addBreadcrumb(Mage::helper('salesrule')->__('Subscription'), Mage::helper('salesrule')->__('List'))
        ;
        return $this;
    }
    public function indexAction()
    {		
        $this->_title($this->__('Subscription'))->_title($this->__('List'));
        $this->loadLayout();
		$this->_setActiveMenu('subscription/index');
		$this->_addContent($this->getLayout()->createBlock('aroma_subscription/adminhtml_list'));
        $this->renderLayout();
    }
	
	public function viewAction() {
		$id = $this->getRequest()->getParam("id");
		$this->_title($this->__('Subscription'))->_title($this->__('View'));
        $this->loadLayout();
		$this->_setActiveMenu('subscription/index');
		$this->_addContent($this->getLayout()->createBlock('aroma_subscription/adminhtml_view'));
        $this->renderLayout();
	}
	
	public function editAction()
    {
        $this->_title($this->__('Subscription'))->_title($this->__('Change Shipment Status'));

        $orderDateId     = $this->getRequest()->getParam('id');
        $orderDateModel  = Mage::getModel('subscription/suborderdate')->load($orderDateId);

        if ($orderDateModel->getId() || $orderDateId == 0) {
            $this->_title($this->__('Edit Shipment Status'));
            Mage::register('order_date', $orderDateModel);

            $this->loadLayout();
            $this->_setActiveMenu('subscription/index');
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Plan Manager'), Mage::helper('adminhtml')->__('Plan Manager'), $this->getUrl('*/*/'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Edit Shipment Status'), Mage::helper('adminhtml')->__('Edit Shipment Status'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('aroma_subscription/adminhtml_view_edit'))
                ->_addLeft($this->getLayout()->createBlock('aroma_subscription/adminhtml_view_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('The plan does not exist.'));
            $this->_redirect('*/*/');
        }
    }
	
	public function saveAction(){
		$data = $this->getRequest()->getPost();
		try{
		if($data) {
			$orderDateId     = $this->getRequest()->getParam('id');
			$orderDateModel  = Mage::getModel('subscription/suborderdate')->load($orderDateId);
			$mail_data = Mage::getModel('subscription/suborderdate')->getCollection();
			$mail_data->addFieldToFilter("main_table.id",$orderDateId);
			$mail_data->getSelect()->joinLeft("aceav_subscription","aceav_subscription.id = main_table.eav_sub_id",array("order_id"));
			$mail_data = $mail_data->getData();
			$order_id = $mail_data[0]['order_id'];
			$order_status = array("1"=>"Processing","2"=>"Complete","3"=>"Intransit","4"=>"Cancelled");
			$order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
			$shippingAddress = $order->getShippingAddress();
			$status = $order_status[$data['status']];
			if($orderDateModel->getStatus() != $data['status']) {
				$senderName = Mage::getStoreConfig('trans_email/ident_general/name');
		$senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');
		$sender  = array(
					'name'=> $senderName,
					'email' => $senderEmail
					);
				//$adminEmail = "vasanthrangaraju@gmail.com";
				$mailTemplate = Mage::getModel('core/email_template');
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
                $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        Mage::getStoreConfig("subscription/email/email_template"),
                        $sender,
                        $order->getCustomerEmail(),
                        $order->getCustomerFirstname(),
                        array('data' => array("order_id"=>$order_id,"order_status"=>$order_status,"comment"=>$data['comment']))
                    );

                if (!$mailTemplate->getSentSuccess()) {
                    //throw new Exception();
                }
					
			}	
			$orderDateModel->setShipDate($data['ship_date']);
			$orderDateModel->setStatus($data['status']);
			$orderDateModel->save();
			$eav_sub_id = $orderDateModel->getEavSubId();
			
			$orderSubStatus = Mage::getModel('subscription/suborderdate')->getCollection();
			$orderSubStatus->addFieldToFilter("eav_sub_id",$eav_sub_id);
			
			$completedStatus = Mage::getModel('subscription/suborderdate')->getCollection();
			$completedStatus->addFieldToFilter("eav_sub_id",$eav_sub_id);
			$completedStatus->addFieldToFilter("status",2);
			
			if($orderSubStatus->getSize() == $completedStatus->getSize()) {
					$statusUpdate = Mage::getModel('subscription/subscription')->load($eav_sub_id);
					$statusUpdate->setStatus(2);
					$statusUpdate->save();
			}
			if($this->getRequest()->getParam("id"))
				$this->_redirect('*/*/', array('id' => $this->getRequest()->getParam('id')));
			else	
				$this->_redirect('*/*/new');
			return;
		}
		}catch(Exception $e){
			echo $e->getMessage();
			echo "error occured";
			exit;
		}
	}
}
?>