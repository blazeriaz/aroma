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
}
?>