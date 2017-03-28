<?php
class Aroma_Subscription_Block_Adminhtml_Plan_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
		
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('plan_form', array('legend'=>Mage::helper('adminhtml')->__('Plan information')));
		$fieldset->addType('image', 'Aroma_Subscription_Block_Adminhtml_Helper_Image_Required');
        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('adminhtml')->__('Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'name',
        ));
		
		$fieldset->addField('image', 'image', array(
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
		$base_price = Mage::getStoreConfig('subscription/subscription/basic');
		$deluxe_price = Mage::getStoreConfig('subscription/subscription/deluxe');
		$fieldset->addField('price', 'text', array(
            'label'     => Mage::helper('adminhtml')->__('Offer in %'),
            'class'     => 'required-entry validate-number validate-digits-range digits-range-0-99',
            'required'  => true,
			'onchange'	=> 'setPriceOnOffer('.$base_price.','.$deluxe_price.')',
            'name'      => 'price'
        ))->setAfterElementHtml("<p id='offer_price' style='display:none;'>Base Price - <small id='base_price'></small><br>
								Deluxe Price - <small id='deluxe_price'></small><br></p>
                         <script type=\"text/javascript\">
                            function setPriceOnOffer(default_base_price,default_deluxe_price){ 
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
