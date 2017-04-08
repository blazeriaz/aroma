<?php
class Aroma_Subscription_Block_Adminhtml_View_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
  {
	
      parent::__construct();
      $this->setId('id');
      $this->setDestElementId('edit_form'); // this should be same as the form id define above
      $this->setTitle(Mage::helper('adminhtml')->__('Shipment Information'));
  }
 
  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('adminhtml')->__('Shipment Status'),
          'title'     => Mage::helper('adminhtml')->__('Shipment Status'),
          'content'   => $this->getLayout()->createBlock('aroma_subscription/adminhtml_view_edit_tab_form')->toHtml(),
      ));
      
      return parent::_beforeToHtml();
  }
}
