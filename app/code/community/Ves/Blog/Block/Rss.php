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
class Ves_Blog_Block_Rss extends Mage_Rss_Block_Abstract
{
    /**
     * @var string $_config
     *
     * @access protected
     */
    protected $_config = '';

    /**
     * @var string $_config
     *
     * @access protected
     */
    protected $_listDesc = array();

    /**
     * @var string $_config
     *
     * @access protected
     */
    protected $_show = 0;
    protected $_theme = "";
  	/**
  	 * Contructor
  	 */
  	public function __construct($attributes = array()){

        $this->convertAttributesToConfig($attributes);

        parent::__construct();

        if(!Mage::getStoreConfig('ves_blog/general_setting/show')){
          return;
        }

        $my_template = "";
        if(isset($attributes['template']) && $attributes['template']) {
          $my_template = $attributes['template'];
        }elseif($this->hasData("template")) {
          $my_template = $this->getData("template");
        }else {
          $my_template = "ves/blog/block/rss.phtml";
        }

        $this->setTemplate($my_template);

        $enable_cache = $this->getConfig("enable_cache", 1 );
        $this->addData(array('cache_lifetime' => 600));

        $this->addCacheTag(array(
          Mage_Core_Model_Store::CACHE_TAG,
          Mage_Cms_Model_Block::CACHE_TAG,
          Ves_Blog_Model_Config::CACHE_BLOCK_RSS_TAG
          ));
    }

  	/**
       * Get Key pieces for caching block content
       *
       * @return array
       */
    public function getCacheKeyInfo()
    {
      return array(
       "VES_BLOG_BLOCK_RSS",
       $this->getNameInLayout(),
       Mage::app()->getStore()->getId(),
       Mage::getDesign()->getPackageName(),
       Mage::getDesign()->getTheme('template'),
       Mage::getSingleton('customer/session')->getCustomerGroupId(),
       'template' => $this->getTemplate(),
       );
    }

    public function _toHtml(){
        if(!Mage::getStoreConfig('ves_blog/general_setting/show')){
          return;
        }
        if(!Mage::getStoreConfig('ves_blog/general_setting/enable_rss_link')){
          return;
        }
        $cat_id = $this->getConfig("cat");
        $category = null;
        $route = Mage::getStoreConfig('ves_blog/general_setting/route');

        if(!$cat_id) {
          $newurl = $this->getUrl($route.'/rss');
          $title = Mage::helper('ves_blog')->__('Blog Posts (All time)');
        } else {
          $newurl = $this->getUrl($route.'/rss/latest/cat/'.$cat_id);
          $category = Mage::getModel('ves_blog/category')->load( $cat_id );
          $title = Mage::helper('ves_blog')->__('Blog Posts (In category: %s)', $category->getTitle());
        }
         
        $rssObj = Mage::getModel('rss/rss');
        $data = array('title' => $title,
                    'description' => $title,
                    'link'        => $newurl,
                    'charset'     => 'UTF-8',
                );

        $rssObj->_addHeader($data);

        $limit = (int)$this->getRequest()->getParam( "limit" );

        if(!$limit) {
            $limit = Mage::getStoreConfig('ves_blog/general_setting/rss_limit');
            $limit = !$limit?15:(int)$limit;
        }

        $orderby = $this->getRequest()->getParam( "orderby" );
        $orderway = $this->getRequest()->getParam( "orderway" );
        $default_orderby = Mage::getStoreConfig('ves_blog/general_setting/rss_sort_orderby');
        $default_orderway = Mage::getStoreConfig('ves_blog/general_setting/rss_sort_orderway');

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

        if($cat_id) {
          $_postCollection = Mage::getModel( 'ves_blog/post' )
                            ->getCollection()
                            ->addCategoryFilter( $cat_id )
                            ->setOrder( $orderby, $orderway )
                            ->setPageSize( $limit )
                            ->setCurPage( 1 );
        } else {
          $_postCollection = Mage::getModel( 'ves_blog/post' )
                            ->getCollection()
                            ->setOrder( $orderby, $orderway )
                            ->setPageSize( $limit )
                            ->setCurPage( 1 );
        }

        $_postCollection->getSelect()->reset(Zend_Db_Select::COLUMNS)
                        ->columns('post_id as id');
        
        if ($_postCollection) {
            $args = array('rssObj' => $rssObj);
            foreach ($_postCollection as $_post) {
              $args['post'] = $_post;
              $this->addBlogPostsXmlCallback($args);
            }
        }

        return $rssObj->createRssXml();
  }

  public function addBlogPostsXmlCallback($args)
  {
      $post = Mage::getModel('ves_blog/post')->load( $args['post']['id'] );
      $extendedDescr = "<p>" . $post->getDescription() . "</p>";

      $description = '<ul>'
      . '<li><a href="'.$post->getURL().'"><img src="'
      . $post->getImageURL( 's' )
      . '" border="0" align="left" height="75" width="75"></a></li>'
      . '<li  style="text-decoration:none;">' . $extendedDescr;
       
      $description .= '</li></ul>';

      $rssObj = $args['rssObj'];
      $data = array(
        'title'         => $post->getTitle(),
        'link'          => $post->getURL(),
        'description'   => $description,
      );
      
      $rssObj->_addEntry($data);
  }

  public function convertAttributesToConfig($attributes = array()) {
      if($attributes) {
        foreach($attributes as $key=>$val) {
          $this->setConfig($key, $val);
        }
      }
    }
    public function getGeneralConfig( $val ){
      return Mage::getStoreConfig( "ves_blog/general_setting/".$val );
    }

    /**
     * get value of the extension's configuration
     *
     * @return string
     */
    public function getConfig( $key, $panel='module_setting', $default = ""){
      $return = "";
      $value = $this->getData($key);
        //Check if has widget config data
      if($this->hasData($key) && $value !== null) {
        if($key == "latestmod_desc") {
          $value = base64_decode($value);
        }
        if($value == "true") {
          return 1;
        } elseif($value == "false") {
          return 0;
        }

        return $value;

      } else {

        if(isset($this->_config[$key])){
          $return = $this->_config[$key];
        }else{
          $return = Mage::getStoreConfig("ves_blog/$panel/$key");
        }
        if($return == "" && !$default) {
          $return = $default;
        }

      }

      return $return;
    }
    public function getListConfig( $key ){
      return  Mage::getStoreConfig('ves_blog/list_setting/'.$key);
    }
    /**
       * overrde the value of the extension's configuration
       *
       * @return string
       */
    function setConfig($key, $value) {
      if($value == "true") {
        $value =  1;
      } elseif($value == "false") {
        $value = 0;
      }
      if($value != "") {
        $this->_config[$key] = $value;
      }

      return $this;
    }

}