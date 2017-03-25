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
class Ves_Contentslider_Model_Config extends Mage_Catalog_Model_Product_Media_Config {

    const CACHE_BLOCK_TAG = 'ves_contentslider_block';
    const CACHE_WIDGET_TAG = 'ves_contentslider_widget';

    public function getBaseMediaPath() {
        return Mage::getBaseDir('media') .DS. 'contentslider';
    }

    public function getBaseMediaUrl() {
        return Mage::getBaseUrl('media') . 'contentslider';
    }

    public function getBaseTmpMediaPath() {
        return Mage::getBaseDir('media') .DS. 'tmp' .DS. 'contentslider';
    }

    public function getBaseTmpMediaUrl() {
        return Mage::getBaseUrl('media') . 'tmp/contentslider';
    }

}