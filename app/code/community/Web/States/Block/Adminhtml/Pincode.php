<?php
class Web_States_Block_Adminhtml_Pincode extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_pincode';
        $this->_blockGroup = 'web_states';
        $this->_headerText = Mage::helper('web_states')->__('Pincode Manager');
        parent::__construct();
    }
}