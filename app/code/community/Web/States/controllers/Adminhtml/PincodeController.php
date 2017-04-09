<?php
class Web_States_Adminhtml_PincodeController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('cms/web_states');
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $pincode = Mage::getModel('web_states/pincode')->load($id);

        if ($pincode->getId() || $id == 0) {
            $this->_initAction();
            Mage::register('pincode_data', $pincode);
            $this->_addBreadcrumb(Mage::helper('web_states')->__('Pincode Manager'), Mage::helper('web_states')->__('Pincode Manager'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('web_states/adminhtml_pincode_edit'))
                ->_addLeft($this->getLayout()->createBlock('web_states/adminhtml_pincode_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('web_states')->__('Pincode does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        $request = $this->getRequest();

        if ($this->getRequest()->getPost()) {
            $id = $request->getParam('id');
            try {
				
                $pincode = Mage::getModel('web_states/pincode');
				if($id != "") {
					$pincode->load($id);
				}	
				
                $pincode->setPincode($this->getRequest()->getPost('pincode'))
                    ->setIsActive(1)
                    ->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->getPincodeData(false);
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setStateData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('region_id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

   
    public function massDeleteAction()
    {
        $stateIds = $this->getRequest()->getParam('web_states');
        if (!is_array($stateIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select pincode(s).'));
        } else {
            try {
                $state = Mage::getModel('web_states/pincode');
                foreach ($stateIds as $stateId) {
                    $state->load($stateId)
                        ->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were deleted.', count($stateIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');

    }

}