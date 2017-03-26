<?php
 
class Aroma_Subscription_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('subscription/index')
            ->_addBreadcrumb(Mage::helper('salesrule')->__('Subscription'), Mage::helper('salesrule')->__('Payment Plans'))
        ;
        return $this;
    }
    public function indexAction()
    {
			
        $this->_title($this->__('Subscription'))->_title($this->__('Payment Plans'));
        $this->loadLayout();
		$this->_setActiveMenu('subscription/index');
		$this->_addContent($this->getLayout()->createBlock('aroma_subscription/adminhtml_plan'));
        $this->renderLayout();
    }
	
	public function newAction(){
		$this->getRequest()->setParam('id', 0);
        $this->_forward('edit');
	}
	
    public function editAction()
    {
        $this->_title($this->__('Subscription'))->_title($this->__('Payment Plans'));

        $planId     = $this->getRequest()->getParam('id');
        $planModel  = Mage::getModel('subscription/paymentplan')->load($planId);

        if ($planModel->getId() || $planId == 0) {
            $this->_title($planModel->getId() ? $planModel->getName() : $this->__('New Plan'));
		
            Mage::register('plan_data', $planModel);

            $this->loadLayout();
            $this->_setActiveMenu('subscription/index');
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Plan Manager'), Mage::helper('adminhtml')->__('Plan Manager'), $this->getUrl('*/*/'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Edit Plan'), Mage::helper('adminhtml')->__('Edit Plan'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('aroma_subscription/adminhtml_plan_edit'))
                ->_addLeft($this->getLayout()->createBlock('aroma_subscription/adminhtml_plan_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('The plan does not exist.'));
            $this->_redirect('*/*/');
        }
    }
	
	public function saveAction() {
		$post = $this->getRequest()->getPost();
		if ( $post ) {
            if($this->getRequest()->getPost('no_of_ship')){
                $collection = Mage::getModel('subscription/paymentplan')->getCollection();
                $collection->addFieldToFilter('no_of_ship',$this->getRequest()->getPost('no_of_ship'));
				if($this->getRequest()->getParam("id"))
				$collection->addFieldToFilter('id', array('nin' => $this->getRequest()->getParam("id")));	
                if(count($collection)){
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('No. of Shipment already exists'));
					Mage::getSingleton('adminhtml/session')->setPlanData($this->getRequest()->getPost());
					if($this->getRequest()->getParam("id"))
						$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
					else	
						$this->_redirect('*/*/new');
                    return;
                }
            }
			try {
                $postData = $this->getRequest()->getPost();
				if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
                try
                {
                    $path = Mage::getBaseDir('media') . '/blog/';
                    $fname = $_FILES['image']['name']; //file name
                    $fullname = $path.$fname;
                    $uploader = new Varien_File_Uploader('file'); //load class
                    $uploader->setAllowedExtensions(array('image','jpg')); //Allowed extension for file
                    $uploader->setAllowCreateFolders(true); //for creating the directory if not exists
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $uploader->save($path, $fname); //save the
                }
                catch (Exception $e)
                {
                    $fileType = "Invalid file format";
                }
            }
                $planModel = Mage::getModel('subscription/paymentplan');
				if($this->getRequest()->getParam('id'))
                $planModel->setId($this->getRequest()->getParam('id'));
				else
                $planModel->setCreated(date("Y-m-d H:i:s"));
			
				$planModel->setModified(date("Y-m-d H:i:s"))
                    ->setName($postData['name'])
                    ->setNoOfShip($postData['no_of_ship'])
					->setPrice($postData['price'])
					->setImage($fname)
					->setStatus($postData['status']);
                $planModel->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setPlanData(false);

                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setPlanData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
		
	}
	
	public function gridAction(){
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock("aroma_subscription/adminhtml_plan_grid")->toHtml()); 
    }
}
?>