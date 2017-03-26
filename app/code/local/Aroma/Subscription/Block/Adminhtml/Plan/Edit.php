<?php
class Aroma_Subscription_Block_Adminhtml_Plan_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                  
        $this->_objectId = 'id'; 
        $this->_blockGroup = 'aroma_subscription';
        $this->_controller = 'adminhtml_plan';
		$this->_mode = 'edit'; 
         
        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('adminhtml')->__('Delete'));

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
		
    }
 
    public function getHeaderText()
    {
        return Mage::helper('adminhtml')->__('Subscription Plan');
    }
}