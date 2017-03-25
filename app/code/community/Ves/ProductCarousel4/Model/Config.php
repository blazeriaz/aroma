<?php
class Ves_ProductCarousel4_Model_Config extends Mage_Catalog_Model_Product_Media_Config {

    const CACHE_BLOCK_TAG = 'ves_productcarousel4_block';
    const CACHE_WIDGET_TAG = 'ves_productcarousel4_widget';

    public function getBaseMediaPath() {
        return Mage::getBaseDir('media') .DS. 'productcarousel4';
    }

    public function getBaseMediaUrl() {
        return Mage::getBaseUrl('media') . 'productcarousel4';
    }

    public function getBaseTmpMediaPath() {
        return Mage::getBaseDir('media') .DS. 'tmp' .DS. 'productcarousel4';
    }

    public function getBaseTmpMediaUrl() {
        return Mage::getBaseUrl('media') . 'tmp/productcarousel4';
    }

}