<?php
class Aroma_Subscription_Block_Adminhtml_Plan_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
  {
	
      parent::__construct();
      $this->setId('id');
      $this->setDestElementId('edit_form'); // this should be same as the form id define above
      $this->setTitle(Mage::helper('adminhtml')->__('Subscription Information'));
  }
 
  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('adminhtml')->__('Plan Information'),
          'title'     => Mage::helper('adminhtml')->__('Plan Information'),
          'content'   => $this->getLayout()->createBlock('aroma_subscription/adminhtml_plan_edit_tab_form')->toHtml(),
      ));
      
      return parent::_beforeToHtml();
  }
}
