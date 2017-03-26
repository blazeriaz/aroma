<?php
class Aroma_Subscription_Block_Adminhtml_Plan_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('plan_form', array('legend'=>Mage::helper('adminhtml')->__('Plan information')));
		
        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('adminhtml')->__('Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'name',
        ));
		
		$fieldset->addField('image', 'file', array(
            'label'     => Mage::helper('adminhtml')->__('Image'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'image',
        ));
		
		$fieldset->addField('no_of_ship', 'text', array(
            'label'     => Mage::helper('adminhtml')->__('Number of Week / Shipment'),
            'class'     => 'required-entry validate-number',
            'required'  => true,
            'name'      => 'no_of_ship',
        ));
		
		$fieldset->addField('price', 'text', array(
            'label'     => Mage::helper('adminhtml')->__('Offer in %'),
            'class'     => 'required-entry validate-number validate-digits-range digits-range-0-99',
            'required'  => true,
			'onchange'	=> 'setPriceOnOffer(this.value)',
            'name'      => 'price'
        ))->setAfterElementHtml("<p id='offer_price' style='display:none;'>Base Price - <small id='base_price'></small><br>
								Deluxe Price - <small id='deluxe_price'></small><br></p>
                         <script type=\"text/javascript\">
                            function setPriceOnOffer(selectElement){ 
								var default_base_price = 18.99;
								var default_deluxe_price = 21.99;
								var offer_percentage = $('price').value;
								var base_price = (default_base_price * offer_percentage)/100;
								var deluxe_price = (default_deluxe_price * offer_percentage)/100;
                               $('base_price').update(parseFloat(default_base_price - base_price).toFixed(2));
							   $('deluxe_price').update(parseFloat(default_deluxe_price - deluxe_price).toFixed(2));
							   $('offer_price').show();
                            }
                         </script>");

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('adminhtml')->__('Status'),
            'name'      => 'status',
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('adminhtml')->__('Active'),
                ),

                array(
                    'value'     => 0,
                    'label'     => Mage::helper('adminhtml')->__('Inactive'),
                ),
            ),
        ));


        if( Mage::getSingleton('adminhtml/session')->getPlanData() ) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getPlanData());
            Mage::getSingleton('adminhtml/session')->setPlanData(null);
        } elseif( Mage::registry('plan_data') ) {
            $form->setValues(Mage::registry('plan_data')->getData());

            $fieldset->addField('was_closed', 'hidden', array(
                'name'      => 'was_closed',
                'no_span'   => true,
                'value'     => Mage::registry('plan_data')->getClosed()
            ));
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
