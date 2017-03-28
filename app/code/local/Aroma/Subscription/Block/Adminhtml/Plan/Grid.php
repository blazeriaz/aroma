<?php
 /*------------------------------------------------------------------------
  # VenusTheme Brand Module 
  # ------------------------------------------------------------------------
  # author:    VenusTheme.Com
  # copyright: Copyright (C) 2012 http://www.venustheme.com. All Rights Reserved.
  # @license: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Websites: http://www.venustheme.com
  # Technical Support:  http://www.venustheme.com/
-------------------------------------------------------------------------*/
class Aroma_Subscription_Block_Adminhtml_Plan_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    public function __construct() {
        parent::__construct();
        $this->setId('id');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
	
	protected function _prepareLayout() {
	
        $this->setChild('add_new_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                'label'     => Mage::helper('ves_brand')->__('Add Record'),
                'onclick'   => "setLocation('".$this->getUrl('*/*/add')."')",
                'class'   => 'add'
                ))
        );
	}
	
    protected function _prepareCollection() {
        $collection = Mage::getModel('subscription/paymentplan')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
	
	
    protected function _prepareColumns() {  
	 
        $this->addColumn('id', array(
                'header'    => Mage::helper('ves_brand')->__('ID'),
                'align'     =>'center',
                'width'     => '50px',
                'index'     => 'id',
        ));
		$this->addColumn('name', array(
                'header'    => Mage::helper('ves_brand')->__('Title'),
                'align'     =>'left',
                'index'     => 'name',
        ));
		$this->addColumn('image', array(
                'header'    => Mage::helper('ves_brand')->__('Image'),
                'align'     =>'center',
                'width'     => '120px',
                'index'     => 'image',
                'renderer'  => 'Aroma_Subscription_Block_Adminhtml_Renderer_Image'
        )); 

		
		$this->addColumn('no_of_ship', array(
                'header'    => Mage::helper('ves_brand')->__('No of shipment'),
                'align'     =>'left',
                'index'     => 'no_of_ship',
        ));	
        $this->addColumn('price', array(
                'header'    => Mage::helper('ves_brand')->__('Percentage in offer'),
                'align'     =>'left',
                'index'     => 'price',
        ));
		
		$this->addColumn('status', array(
                'header'    => Mage::helper('ves_brand')->__('Status'),
                'align'     => 'left',
                'width'     => '80px',
                'index'     => 'status',
                'type'      => 'options',
                'options'   => array(
                        1 => Mage::helper('ves_brand')->__('Enabled'),
                        0 => Mage::helper('ves_brand')->__('Disabled'),
                ),
        ));

        return parent::_prepareColumns();
    }
    /**
     * Helper function to do after load modifications
     *
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
    /**
     * Helper function to add store filter condition
     *
     * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection Data collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column Column information to be filtered
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        
        $this->getCollection()->addStoreFilter($value);
    }

    protected function _prepareMassaction() { 
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem('delete', array(
                'label'    => Mage::helper('ves_brand')->__('Delete'),
                'url'      => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('ves_brand')->__('Are you sure?')
        ));

        $statuses = array(
                1 => Mage::helper('ves_brand')->__('Enabled'),
                2 => Mage::helper('ves_brand')->__('Disabled')
				);
        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
                'label'=> Mage::helper('ves_brand')->__('Change status'),
                'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                        'visibility' => array(
                                'name' => 'status',
                                'type' => 'select',
                                'class' => 'required-entry',
                                'label' => Mage::helper('ves_brand')->__('Status'),
                                'values' => $statuses
                        )
                )
        ));
        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}