<?php
class Aroma_Subscription_Block_Adminhtml_View_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
		
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('plan_form', array('legend'=>Mage::helper('adminhtml')->__('Plan information')));
		$fieldset->addField('ship_date', 'date', array(
			'name'               => 'ship_date',
			'label'              => Mage::helper('adminhtml')->__('Shipment Date'),
			'tabindex'           => 1,
			'image'              => $this->getSkinUrl('images/grid-cal.gif'),
			'format'             => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT) ,
			'value'              => 'ship_date' 
		));
		$options = array(array(
                    'value'     => 1,
                    'label'     => Mage::helper('adminhtml')->__('Processing'),
                ),

                array(
                    'value'     => 2,
                    'label'     => Mage::helper('adminhtml')->__('Complete'),
                ),
				array(
                    'value'     => 3,
                    'label'     => Mage::helper('adminhtml')->__('Intransit'),
                ),
				array(
                    'value'     => 4,
                    'label'     => Mage::helper('adminhtml')->__('Cancelled'),
                ));
        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('adminhtml')->__('Status'),
            'name'      => 'status',
            'values'    => $options,
        ));

		$fieldset->addField('textarea', 'textarea', array(
          'label'     => Mage::helper('adminhtml')->__('Comment for Email'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
		  'onclick' => "",
		  'onchange' => "",
		  'value'  => '<b><b/>',
		  'after_element_html' => '<small>Comments</small>',
		  'tabindex' => 1
		));
		
        if( Mage::getSingleton('adminhtml/session')->getOrderDate() ) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getOrderDate());
            Mage::getSingleton('adminhtml/session')->setOrderDate(null);
        } elseif( Mage::registry('order_date') ) {
            $form->setValues(Mage::registry('order_date')->getData());

            $fieldset->addField('was_closed', 'hidden', array(
                'name'      => 'was_closed',
                'no_span'   => true,
                'value'     => Mage::registry('order_date')->getClosed()
            ));
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
	

}
