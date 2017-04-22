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
class Aroma_Subscription_Block_Adminhtml_Expirelist_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    public function __construct() {
        parent::__construct();
        $this->setId('id');
        $this->setDefaultSort('ship_date');
        $this->setDefaultDir('ASC');
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
		$current_date = date("Y-m-d H:i:s");
        $collection = Mage::getModel('subscription/suborderdate')->getCollection();
        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('MIN(ship_date) as ship_date');
        $collection->addFieldToFilter("main_table.status",array("in"=>array(1,3)));
        $collection->getSelect()->join("aceav_subscription","aceav_subscription.id = main_table.eav_sub_id",array("aceav_subscription.title","aceav_subscription.id","aceav_subscription.order_id"));
		$collection->addFieldToFilter("main_table.ship_date",array("lt"=>$current_date));
		$collection->getSelect()->group("main_table.eav_sub_id");
        $this->setCollection($collection);
        parent::_prepareCollection();
        foreach($collection as $data) {
            $ship_order_date = Mage::getModel('subscription/suborderdate')->getCollection();
            $ship_order_date->getSelect()->where("ship_date = '".$data->ship_date."'");
            $ship_order_date->getSelect()->where("eav_sub_id = ".$data->id);
            $order_date = $ship_order_date->getData();
            $data->id = $order_date[0]['id'];
            //$data->
        }
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
		$this->addColumn('order_id', array(
                'header'    => Mage::helper('ves_brand')->__('Order Id'),
                'align'     =>'left',
                'index'     => 'order_id',
        ));
		$this->addColumn('ship_date', array(
                'header'    => Mage::helper('ves_brand')->__('Ship Date'),
                'align'     => 'left',  
                'index'     => 'ship_date',
        ));
		
		$link= Mage::helper('adminhtml')->getUrl('admin_subscription/adminhtml_list/edit/id/$id');
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
