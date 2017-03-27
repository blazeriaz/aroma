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
                   $uploader = new Varien_File_Uploader('image'); //load class
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); //Allowed extension for file
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
			
				  
					   
				$arrData = array(
							'created' => date("Y-m-d H:i:s"),
							'modified'=> date("Y-m-d H:i:s"),
							'name'=> $postData['name'],
							'no_of_ship'=> $postData['no_of_ship'],
							'price'=> $postData['price'],
							'image'=> $fname,
							'status'=> $postData['status'],
							); 
							
				if($id = $this->getRequest()->getParam('id')){
					$rr = Mage::getModel('subscription/paymentplan')->getCollection();
					
				$model = Mage::getModel('subscription/paymentplan')->load($id);
				$model->setName($postData['name']);
				$model->setModified($postData['name']);
				$model->setNoOfShip($postData['no_of_ship']);
				$model->setPrice($postData['price']);
				$model->setImage($fname);
				$model->setStatus($postData['status']);
				
				$model->setId($id)->save();  
				}else{
				$model = Mage::getModel('subscription/paymentplan')->setData($arrData ); 
				$model->save();				
				}
				
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
	
	public function massDeleteAction()
	{
		$ids = $this->getRequest()->getParam('id');      // $this->getMassactionBlock()->setFormFieldName('tax_id'); from Mage_Adminhtml_Block_Tax_Rate_Grid
		if(!is_array($ids)) {
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('Please select one of the option(s).'));
		} else {
		try {
		$planModel = Mage::getModel('subscription/paymentplan');
		foreach ($ids as $id) {
			$planModel->load($id)->delete();
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(
		Mage::helper('tax')->__(
		'Total of %d record(s) were deleted.', count($ids)
		)
		);
		} catch (Exception $e) {
		Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
		} 
		$this->_redirect('*/*/index');
	}
	
	public function massStatusAction() {
    /*
     * Grid massaction function to activate multiple records from the database
     * */
    $ids = $this->getRequest()->getParam('id');
    if(!is_array($ids)) {
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select the records you wish to activate'));
    } 
    else {
        try {
            $model = Mage::getModel('subscription/paymentplan');
            foreach ($ids as $id) {
                $model->load($id)->setStatus($this->getRequest()->getParam('status'))->save(); 
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(
            Mage::helper('adminhtml')->__('Total of %d record(s) were marked as active.', count($ids)));
        } 
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }
    $this->_redirect('*/*/');
}
}
?>