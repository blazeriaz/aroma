<?php
/*------------------------------------------------------------------------
 # VenusTheme ContentSlider Module 
 # ------------------------------------------------------------------------
 # author:    VenusTheme.Com
 # copyright: Copyright (C) 2012 http://www.venustheme.com. All Rights Reserved.
 # @license: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 # Websites: http://www.venustheme.com
 # Technical Support:  http://www.venustheme.com/
-------------------------------------------------------------------------*/
class Ves_Contentslider_Block_List extends Mage_Core_Block_Template 
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
	public function __construct($attributes = array())
	{	
		if(isset($attributes['bannerId']) && $attributes['bannerId']) {
			$this->setConfig( "bannerId", $attributes['bannerId']);
		}

		$this->convertAttributesToConfig($attributes);
		
		$this->_show = $this->getConfig("show");
 		
		if(!$this->_show) return;

		parent::__construct();
		
		if($this->hasData("template") && $this->getData("template")) {
			$this->setTemplate($this->getData("template"));
		} else {
			$this->setTemplate("ves/contentslider/default.phtml");	
		}
		
		/*Cache Block*/
	      $enable_cache = $this->getConfig("enable_cache", 1 );
	      if(!$enable_cache) {
	        $cache_lifetime = null;
	      } else {
	        $cache_lifetime = $this->getConfig("cache_lifetime", 86400 );
	        $cache_lifetime = (int)$cache_lifetime>0?$cache_lifetime: 86400;
	      }

	      $this->addData(array('cache_lifetime' => $cache_lifetime));
	      $this->addCacheTag(array(
	        Mage_Core_Model_Store::CACHE_TAG,
	        Mage_Cms_Model_Block::CACHE_TAG,
	        Ves_Contentslider_Model_Config::CACHE_BLOCK_TAG
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
           'VES_CONTENTSLIDER_BLOCK_LIST',
           $this->getNameInLayout(),
           Mage::app()->getStore()->getId(),
           Mage::getDesign()->getPackageName(),
           Mage::getDesign()->getTheme('template'),
           Mage::getSingleton('customer/session')->getCustomerGroupId(),
           'template' => $this->getTemplate(),
        );
    }

    public function convertAttributesToConfig($attributes = array()) {
      if($attributes) {
        foreach($attributes as $key=>$val) {
            $this->setConfig($key, $val);
        }
      }
    }
	/**
     * Rendering block content
     *
     * @return string
     */
	function _toHtml() 
	{
		if(!$this->_show) return;
		
		$banner_id = $this->getConfig("bannerId");
		$banner_id = $banner_id?$banner_id:0;
		$banner  = Mage::getModel('ves_contentslider/banner')->load( $banner_id );
		$is_active =  $banner->getData("is_active");
		if((int)$banner_id > 0 && $is_active) {
			$banners = array();
			$params = $banner->getData("params");
			$params = unserialize(base64_decode($params));
			
			if(isset($params['banner_images'])) {
				$banners = $params['banner_images'];
				unset($params['banner_images']);
			}

			$thumb_width = isset($params['width'])?$params['width']: 700;
			$thumb_height = isset($params['height'])?$params['height']: 369;
			$nav_width = isset($params['navimg_width'])?$params['navimg_width']: 100;
			$nav_height = isset($params['navimg_height'])?$params['navimg_height']: 100;
			$params['auto_play_mode'] = $params['auto_play'];
			$params['auto_play'] = $params['auto_play']?"true":"false";


			if($banners) {
				$tmp = array();
				foreach($banners as $i=>$banner) {
					$banner['position'] = isset($banner['position'])?$banner['position']:0;
					$banner['thumb'] = $this->resizeImage( $banner['image'], $thumb_width, $thumb_height);
					$banner['thumb'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$banner['thumb'];

					$banner['image_navigator'] = $this->resizeImage( $banner['image'], $nav_width, $nav_height);
					$banner['image_navigator'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$banner['image_navigator'];

					if($banner['title']) {
						$layers = array();
						foreach($banner['title'] as $key=>$val) {
							$layer = array();
							$layer['caption'] = $val;
							$layer['effect'] = isset($banner['effect'][$key])?$banner['effect'][$key]:"fadeIn";
							$layer['class'] = isset($banner['class'][$key])?$banner['class'][$key]:"";
							$layer['top'] = isset($banner['top'][$key])?$banner['top'][$key]:"0";
							$layer['left'] = isset($banner['left'][$key])?$banner['left'][$key]:"0";
							$layer['caption_position'] = isset($banner['caption_position'][$key])?$banner['caption_position'][$key]:"custom";
							$layers[] = $layer;
						}
						$banner['layers'] = $layers;
						$banner['title'] = "";
					}
					$tmp[] = $banner;
				}
				$banners = Mage::helper("ves_contentslider")->record_sort($tmp, "position");
			}

			$this->assign("setting", $params);
			$this->assign("banners", $banners);
			
			return parent::_toHtml();
		}
		return ;
    }
	
	public function resizeImage( $image, $width, $height ){

		$image= str_replace("/",DS, $image);
		$_imageUrl = Mage::getBaseDir('media').DS.$image;
		$imageResized = Mage::getBaseDir('media').DS."resized".DS."{$width}x{$height}".DS.$image;
		$quality = $this->getConfig("resize_quality");
		if (!file_exists($imageResized)&&file_exists($_imageUrl)) {
			$imageObj = new Varien_Image($_imageUrl);
			$imageObj->constrainOnly(true);
		    $imageObj->keepAspectRatio(true);
		    $imageObj->keepFrame(false);
		    $imageObj->keepTransparency(true);
			$imageObj->resize( $width, $height);
			if($quality) {
		    	$imageObj->quality($quality);
		    }
			$imageObj->save($imageResized);
			
		}
		return 'resized/'."{$width}x{$height}/".str_replace(DS,"/",$image);
	}

	public function buildThumbnail ( $image, $width, $height ) 
	{

		$thumbnailMode = $this->_config['thumbnailMode'];
		$imageProcessor =  Mage::helper('ves_contentslider/vesimage');
			
		$imageProcessor->setStoredFolder();
		$thumbs  = $imageProcessor->resize( $image, $width, $height );

		return $thumbs;
	}
	/**
	 * get value of the extension's configuration
	 *
	 * @return string
	 */
	public function getConfig( $key, $panel='general_setting', $default = ""){
	    $return = "";
	    $value = $this->getData($key);
	    //Check if has widget config data
	    if($this->hasData($key) && $value !== null) {

	      if($value == "true") {
	        return 1;
	      } elseif($value == "false") {
	        return 0;
	      }
	      
	      return $value;
	      
	    } else {

	      if(isset($this->_config[$key])){
	        $return = $this->_config[$key];
	        if($return == "true") {
		        $return = 1;
		    } elseif($return == "false") {
		        $return = 0;
		    }
	      }else{
	        $return = Mage::getStoreConfig("ves_contentslider/$panel/$key");
	      }
	      if($return == "" && !$default) {
	        $return = $default;
	      }

	    }

	    return $return;
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
