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
		if($data) {
			$orderDateId     = $this->getRequest()->getParam('id');
			$orderDateModel  = Mage::getModel('subscription/suborderdate')->load($orderDateId);
			$orderDateModel->setShipDate($data['ship_date']);
			$orderDateModel->setStatus($data['status']);
			$orderDateModel->save();
			if($this->getRequest()->getParam("id"))
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			else	
				$this->_redirect('*/*/new');
			return;
		}
	}
}
?>