<?php
class Web_States_Block_Adminhtml_Pincode_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $id = $this->getRequest()->getParam('id');



        $fieldSet = $form->addFieldset('web_states_form', array('legend' => Mage::helper('web_states')->__('Pincode information')));
        $fieldSet->addField(
            'pincode', 'text', array(
                                         'label'    => Mage::helper('web_states')->__('Pincode'),
                                         'name'     => 'pincode',
                                         'required' => true,
                                    )
        );

       
        if (Mage::getSingleton('adminhtml/session')->getPincodeData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getPincodeData());
            Mage::getSingleton('adminhtml/session')->setStateData(null);
        } elseif (Mage::registry('pincode_data')) {
            $form->setValues(Mage::registry('pincode_data')->getData());
        }
       
        return parent::_prepareForm();

    }
}
