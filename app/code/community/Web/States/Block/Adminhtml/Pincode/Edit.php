<?php
class Web_States_Block_Adminhtml_Pincode_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'web_states';
        $this->_controller = 'adminhtml_pincode';
        $this->_updateButton('save', 'label', Mage::helper('web_states')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('web_states')->__('Delete Item'));

    }

    public function getHeaderText()
    {
        if (Mage::registry('pincode_data') && Mage::registry('pincode_data')->getId()) {
            return Mage::helper('web_states')->__("Edit Item '%s'", $this->escapeHtml(Mage::registry('pincode_data')->getId()));
        } else {
            return Mage::helper('web_states')->__('Add Item');
        }
    }
}
