<?php
/*------------------------------------------------------------------------
 # VenusTheme Pagebuilder Module 
 # ------------------------------------------------------------------------
 # author:    VenusTheme.Com
 # copyright: Copyright (C) 2012 http://www.venustheme.com. All Rights Reserved.
 # @license: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 # Websites: http://www.venustheme.com
 # Technical Support:  http://www.venustheme.com/
-------------------------------------------------------------------------*/
//require_once(Mage::getBaseDir('code').'/community/Ves/Pagebuider/Helper/widgetbase.php');
class Ves_ProductCategories_Block_List extends Mage_Core_Block_Template 
{	
	var $_config = array();
	/**
	 * Contructor
	 */
	public function __construct($attributes = array())
	{	
		/*End init meida files*/
		if( !$this->getConfig('show') ) return;

		$this->_controller = 'productcategories';
		$mediaHelper =  Mage::helper('ves_productcategories/media');
		$mediaHelper->addMediaFile("skin_css", "ves_productcategories/style.css" );
		$my_template = $this->getTemplate();
		if(empty($my_template)) {
			$template = 'ves/productcategories/default.phtml';
			$this->setTemplate( $template );
		}
		parent::__construct();		
	}
	/**
     * Rendering block content
     *
     * @return string
     */
	function _toHtml() 
	{	
		if( !$this->getConfig('show') ) return;
		//$cateids = $this->getConfig('catsid');
		$this->assign( "title", $this->getConfig('title'));
		$this->assign( "prefix", $this->getConfig('prefix') );
		$this->assign( "limit", $this->getConfig('limit') );
		$this->assign( "columns", $this->getConfig('columns') );
		$this->assign( "enable_price", $this->getConfig('enable_price_cate_parent') );
        return parent::_toHtml();
		}

	/**
	 * get value of the extension's configuration
	 *
	 * @return string
	 */
	function getConfig( $key, $panel='ves_productcategories' ){
		if(isset($this->_config[$key])) {
			return $this->_config[$key];
		} else {
			return Mage::getStoreConfig("ves_productcategories/$panel/$key");
		}
	}
}