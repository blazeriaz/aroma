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
class Aroma_Subscription_Block_Adminhtml_List_Grid extends Mage_Adminhtml_Block_Widget_Grid {
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
        $collection = Mage::getModel('subscription/subscription')->getCollection();
		$collection->getSelect()->joinLeft("acsub_order_date","acsub_order_date.eav_sub_id = main_table.id",array("count(eav_sub_id) as total_cnt","SUM(CASE WHEN acsub_order_date.status = 1 THEN 1 ELSE 0 END) as pending_count",
		"SUM(CASE WHEN acsub_order_date.status = 2 THEN 1 ELSE 0 END) as complete_count","SUM(CASE WHEN acsub_order_date.status = 3 THEN 1 ELSE 0 END) as intransit_count","SUM(CASE WHEN acsub_order_date.status = 4 THEN 1 ELSE 0 END) as cancel_count"));
		$collection->getSelect()->group("main_table.id");
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
		$this->addColumn('title', array(
                'header'    => Mage::helper('ves_brand')->__('Title'),
                'align'     =>'left',
                'index'     => 'title',
        ));
		$this->addColumn('total_cnt', array(
                'header'    => Mage::helper('ves_brand')->__('No. of Shipment'),
                'align'     =>'left',
                'index'     => 'total_cnt',
        ));
		$this->addColumn('pending_count', array(
                'header'    => Mage::helper('ves_brand')->__('No. of Pending Shipment'),
                'align'     =>'left',
                'index'     => 'pending_count',
        ));
		$this->addColumn('complete_count', array(
                'header'    => Mage::helper('ves_brand')->__('No. of Complete Shipment'),
                'align'     =>'left',
                'index'     => 'complete_count',
        ));
		$this->addColumn('intransit_count', array(
                'header'    => Mage::helper('ves_brand')->__('No. of Intransit Shipment'),
                'align'     =>'left',
                'index'     => 'intransit_count',
        ));
		$this->addColumn('cancel_count', array(
                'header'    => Mage::helper('ves_brand')->__('No. of Cancel Shipment'),
                'align'     =>'left',
                'index'     => 'cancel_count',
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
                        3 => Mage::helper('ves_brand')->__('Intransist'),
                        4 => Mage::helper('ves_brand')->__('Cancelled'),
                ),
        ));
		
		$link= Mage::helper('adminhtml')->getUrl('admin_subscription/adminhtml_list/view/id/$id');
    $this->addColumn('action_edit', array(
        'header'   => $this->helper('catalog')->__('Action'),
        'width'    => 15,
        'sortable' => false,
        'filter'   => false,
        'type'     => 'action',
        'actions'  => array(
            array(
                'url'     => $link,
                'caption' => $this->helper('catalog')->__('View'),
            ),
        )
    ));
        return parent::_prepareColumns();
    }
    

}