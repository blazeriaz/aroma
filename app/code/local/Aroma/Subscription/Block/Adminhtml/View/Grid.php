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
class Aroma_Subscription_Block_Adminhtml_View_Grid extends Mage_Adminhtml_Block_Widget_Grid {
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
		$id = $this->getRequest()->getParam('id');	
        $collection = Mage::getModel('subscription/suborderdate')->getCollection();
		$collection->addFieldToFilter("eav_sub_id",$id);
		$collection->getSelect()->joinLeft("aceav_subscription","aceav_subscription.id = main_table.eav_sub_id",array('title'));
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
		$this->addColumn('ship_date', array(
                'header'    => Mage::helper('ves_brand')->__('Shipment Date'),
                'align'     =>'left',
                'index'     => 'ship_date',
        ));
		
		$this->addColumn('status', array(
                'header'    => Mage::helper('ves_brand')->__('Status'),
                'align'     => 'left',
                'width'     => '80px',
                'index'     => 'status',
                'type'      => 'options',
                'options'   => array(
                        1 => Mage::helper('ves_brand')->__('Processing'),
                        2 => Mage::helper('ves_brand')->__('Completed'),
						3 => Mage::helper('ves_brand')->__('Intransit'),
						4 => Mage::helper('ves_brand')->__('Cancelled')
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