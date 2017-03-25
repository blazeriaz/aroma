<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_Blog
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves Blog Extension
 *
 * @category   Ves
 * @package    Ves_Blog
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_Blog_Block_Blog_List extends Ves_Blog_Block_Blog_Template
{
	var $_layout_mode = "";

	public function __construct($attributes = array())
	{
		parent::__construct( $attributes );

		$allow_custom_layout = false;

		$mode = $this->getRequest()->getParam( "mode" );
		if($mode) {
			Mage::getModel('core/cookie')->set("ves_blog_layout_mode", $mode, time()+86400,'/');
		} else {
			$mode = Mage::getModel('core/cookie')->get("ves_blog_layout_mode");
		}

		if(!$mode) {
			$mode = $this->getListConfig("list_layout_mode");
			$allow_custom_layout = true;
		}

		$this->_layout_mode = $mode;

		if($allow_custom_layout && $this->hasData("template") && $this->getData("template")) {
			$this->setTemplate($this->getData("template"));
		} else {
			switch ($mode) {
				case 'list':
				case 'grid':
				$this->setTemplate("ves/blog/layout/default.phtml");
				break;
				case 'second':
				$this->setTemplate("ves/blog/layout/second.phtml");
				break;
				case 'masonry':
				$this->setTemplate("ves/blog/layout/masonry.phtml");
				break;
				case 'thumb_view':
				$this->setTemplate("ves/blog/layout/thumb_view.phtml");
				break;
				case 'custom':
				$this->setTemplate("ves/blog/layout/custom.phtml");
				break;
				default:
				$this->setTemplate("ves/blog/list.phtml");
				break;
			}
		}
	}
	public function getLayoutMode() {
		return $this->_layout_mode;
	}
	public function _toHtml(){
		$grid_col_ls = $this->getListConfig("grid_col_ls");
		$grid_col_ls = $grid_col_ls?(int)$grid_col_ls:3;
		$grid_col_ms = $this->getListConfig("grid_col_ms");
		$grid_col_ms = $grid_col_ms?(int)$grid_col_ms:3;
		$grid_col_ss = $this->getListConfig("grid_col_ss");
		$grid_col_ss = $grid_col_ss?(int)$grid_col_ss:2;
		$grid_col_mss = $this->getListConfig("grid_col_mss");
		$grid_col_mss = $grid_col_mss?(int)$grid_col_mss:1;

		$second_image_col = $this->getListConfig("second_image_col");
		$second_image_col = $second_image_col?(int)$second_image_col:6;
		$second_content_col = $this->getListConfig("second_content_col");
		$second_content_col = $second_content_col?(int)$second_content_col:6;

		$this->assign("grid_col_ls", $grid_col_ls);
		$this->assign("grid_col_ms", $grid_col_ms);
		$this->assign("grid_col_ss", $grid_col_ss);
		$this->assign("grid_col_mss", $grid_col_mss);
		$this->assign("second_image_col", $second_image_col);
		$this->assign("second_content_col", $second_content_col);

		return parent::_toHtml();
	}

	protected function _prepareLayout() {
		$tag = $this->getRequest()->getParam( "tag" );
		$archive = $this->getRequest()->getParam( "archive" );
		$author = (int)$this->getRequest()->getParam( "user" );

		$keyword = $this->getRequest()->getParam( "search_query" );
		$keyword = trim($keyword);

		$page_info = $this->getCountingPost();

		$meta_description = $this->getGeneralConfig("metadescription");
		$meta_description = $meta_description?$meta_description:$this->__("Latest Blog Posts");
		$meta_title_prefix = $meta_desc_prefix = "";
		$items_count = (int)$page_info[0];
		$pages_limit = (int)$page_info[1];
		$pages_limit = $pages_limit?$pages_limit:1;

		if($items_count > 1) {
			$default_page = $this->getRequest()->getParam('p') ? $this->getRequest()->getParam('p') : 1;
			$page = $this->getRequest()->getParam('page') ? $this->getRequest()->getParam('page') : $default_page;
			$pages_count = ceil($items_count/$pages_limit);

			
			if($page <= 1){
				$meta_desc_prefix = $this->__("Listings of %s theatres, ", $pages_count);
			} else {
				$curpage_start = $pages_limit*$page-($pages_limit-1);
				$curpage_end = $curpage_start+($pages_limit-1);
				$curpage_end = ($curpage_end > $items_count)?$items_count:$curpage_end;
				$meta_title_prefix = $this->__("Page %s of %s for ", $page, $pages_count);
				$meta_desc_prefix = $this->__("Listings %s-%s (out of %s) theatres, ",$curpage_start,$curpage_end,$items_count);
			}
			//Add prefix for meta description
			$meta_description = $meta_desc_prefix.$meta_description;
		}
		

		if( $tag ){
			$this->setType( "tag" )
			->setPageTitle( $meta_title_prefix.sprintf($this->__("Displaying posts by tag: %s"),$tag) )
			->setHeadInfo( $this->getGeneralConfig("metakeywords"), $meta_description );

		}elseif( $archive ){
			$tmp = explode("_", $archive);
			$year = $month = "";
			if(count($tmp) > 1) {
				$year = $tmp[0];
				$month = date("F", mktime(0, 0, 0, $tmp[1], 10));
				$archive = $month.", ".$year;
			} else {
				$archive = $tmp[0];
			}
			$this->setType( "archive" )
			->setPageTitle( $meta_title_prefix.sprintf($this->__("Displaying posts by archives '%s'"),$archive) )
			->setHeadInfo( $this->getGeneralConfig("metakeywords"), $meta_description );

		}elseif( $author ) {
			$author = Mage::getModel("admin/user")->load( $author );
			$f = $author->getFirstname().' '.$author->getLastname();
			$this->setType( "author" )
			->setPageTitle( $meta_title_prefix.sprintf($this->__("Displaying posts by author: %s"),$f) )
			->setHeadInfo( $this->getGeneralConfig("metakeywords"), $meta_description );
		} elseif($keyword && strlen($keyword) >= 3) {
			$this->setType( "search_query" )
			->setPageTitle( $meta_title_prefix.sprintf($this->__("Search results for '%s'"),$keyword) )
			->setHeadInfo( $this->getGeneralConfig("metakeywords"), $meta_description );
		} else {
			$this->setType( "latest" )
			->setPageTitle( $meta_title_prefix.$this->__("Latest Posts") )
			->setHeadInfo( $this->getGeneralConfig("metakeywords"), $meta_description );

		}
		

		$breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
		if($breadcrumbs) {
			$breadcrumbs->addCrumb( 'home', array( 'label'=>Mage::helper('ves_blog')->__('Home'),
			'title'=>Mage::helper('ves_blog')->__('Go to Home Page'),
			'link' => Mage::getBaseUrl()) );

			$extension = "";
			$breadcrumbs->addCrumb( 'venus_blog', array( 'label' => $this->getGeneralConfig("title"),
				'title' => $this->getGeneralConfig("title"),
				'link'  =>  Mage::getBaseUrl().$this->getGeneralConfig("route").$extension ) );
		}
		

	}

	public function countPosts( $category_id = 0 ){
		$collection = Mage::getModel( 'ves_blog/post' )
		->getCollection();
		if( $this->getType() == "tag" ){
			$collection->addTagsFilter( array($this->getRequest()->getParam( "tag" )) );
		}elseif ( $this->getType() == "author" ){
			$collection->addAuthorFilter( (int)$this->getRequest()->getParam( "user" ) );
		}elseif ( $this->getType() == "archive" ){
			$collection->addArchivesFilter( $this->getRequest()->getParam( "archive" ) );
		}
		$collection->addCategoriesFilter(0);

		return $collection->count();
	}
	public function getCountingPost(){
		$collection = Mage::getModel( 'ves_blog/post' )
							->getCollection();

		$keyword = $this->getRequest()->getParam( "search_query" );
		$keyword = trim($keyword);
		$orderby = $this->getRequest()->getParam( "orderby" );
		$orderway = $this->getRequest()->getParam( "orderway" );

		$default_orderby = $this->getListConfig("sort_orderby");
		$default_orderway = $this->getListConfig("sort_orderway");

		if(!$default_orderby) {
			$default_orderby = "created";
		}

		if(!$default_orderway) {
			$default_orderway = "DESC";
		}

		if(!$orderby && !$orderway) {
			$orderby = $default_orderby;
			$orderway = $default_orderway;
		} elseif($orderby && !$orderway) {
			$orderway = $default_orderway;
		} elseif(!$orderby && $orderway) {
			$orderby = $default_orderby;
		}

		if($keyword && strlen($keyword) >= 3) {
			$collection->addKeywordFilter($keyword);
		}

		if( $this->getType() == "tag" ){
			$collection->addTagsFilter( array($this->getRequest()->getParam( "tag" )) );
		}elseif ( $this->getType() == "author" ){
			$collection->addAuthorFilter( (int)$this->getRequest()->getParam( "user" ) );
		}elseif ( $this->getType() == "archive" ){
			$collection->addArchivesFilter( $this->getRequest()->getParam( "archive" ) );
		}

		$collection->addCategoriesFilter(0)->setOrder( $orderby, $orderway );

		if($this->_layout_mode) {
			$limit = (int)$this->getListConfig("list_limit");
		} else {
			$limit = (int)$this->getListConfig("list_leadinglimit") + (int)$this->getListConfig("list_secondlimit");
		}

		Mage::register( 'paginateTotal', count($collection) );
		Mage::register( "paginateLimitPerPage", $limit );

		return array(count($collection), $limit);
	}

	public function getPosts(){

		$id = $this->getRequest()->getParam('id');
		$default_page = $this->getRequest()->getParam('p') ? $this->getRequest()->getParam('p') : 1;
		$page = $this->getRequest()->getParam('page') ? $this->getRequest()->getParam('page') : $default_page;
		if($this->_layout_mode) {
			$limit = (int)$this->getListConfig("list_limit");
		} else {
			$limit = (int)$this->getListConfig("list_leadinglimit") + (int)$this->getListConfig("list_secondlimit");
		}

		$collection = Mage::getModel( 'ves_blog/post' )
		->getCollection()
		->addEnableFilter(1);

		$keyword = $this->getRequest()->getParam( "search_query" );
		$keyword = trim($keyword);
		$orderby = $this->getRequest()->getParam( "orderby" );
		$orderway = $this->getRequest()->getParam( "orderway" );

		$default_orderby = $this->getListConfig("sort_orderby");
		$default_orderway = $this->getListConfig("sort_orderway");

		if(!$default_orderby) {
			$default_orderby = "created";
		}

		if(!$default_orderway) {
			$default_orderway = "DESC";
		}

		if(!$orderby && !$orderway) {
			$orderby = $default_orderby;
			$orderway = $default_orderway;
		} elseif($orderby && !$orderway) {
			$orderway = $default_orderway;
		} elseif(!$orderby && $orderway) {
			$orderby = $default_orderby;
		}

		if($keyword && strlen($keyword) >= 3) {
			$collection->addKeywordFilter($keyword);
		}

		if( $this->getType() == "tag" ){
			$collection->addTagsFilter( array($this->getRequest()->getParam( "tag" )) );
		}elseif ( $this->getType() == "author" ){
			$collection->addAuthorFilter( (int)$this->getRequest()->getParam( "user" ) );
		}elseif ( $this->getType() == "archive" ){
			$collection->addArchivesFilter( $this->getRequest()->getParam( "archive" ) );
		}

		$collection->addCategoriesFilter(0)->setOrder( $orderby, $orderway )
		->setPageSize( $limit )
		->setCurPage( $page );

		return $collection;
	}

	public function getCountingComment( $post_id = 0){

		$comment = Mage::getModel('ves_blog/comment')->getCollection()
		->addEnableFilter( 1  )
		->addStoreFilter(Mage::app()->getStore()->getId())
		->addPostFilter( $post_id  );
		return count($comment);
	}

}