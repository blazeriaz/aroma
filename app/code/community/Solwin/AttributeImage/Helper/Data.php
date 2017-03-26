<?php

class Solwin_AttributeImage_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getAttributeImage($optionId) {
        $images = $this->getAttributeImages();
        $image = array_key_exists($optionId, $images) ? $images[$optionId] : '';
        if ($image && (strpos($image, 'http') !== 0)) {
            $image = Mage::getDesign()->getSkinUrl($image);
        }
        return $image;
    }
	
	public function getAttributeDescription($optionId){
		 $descriptions = $this->getAttributeDescriptions();
		  $description = array_key_exists($optionId, $descriptions) ? $descriptions[$optionId] : '';
		 return $description;
	}
	public function getAttributeDescriptions() {
        $descriptions = Mage::getResourceModel('eav/entity_attribute_option')->getAttributeDescriptions();
        return $descriptions;
    }
	
	public function getAttributeParams($optionId){
		 $paramss = $this->getAttributeParamss();
		  $params = array_key_exists($optionId, $paramss) ? $paramss[$optionId] : '';
		 return $params;
	}
	public function getAttributeParamss() {
        $paramss = Mage::getResourceModel('eav/entity_attribute_option')->getAttributeParamss();
        return $paramss;
    }
	
	 
    public function getAttributeImages() {
        $images = Mage::getResourceModel('eav/entity_attribute_option')->getAttributeImages();
        return $images;
    }

    public function getAttributeThumb($optionId) {
        $images = $this->getAttributeThumbs();
        $image = array_key_exists($optionId, $images) ? $images[$optionId] : '';
        if ($image && (strpos($image, 'http') !== 0)) {
            $image = Mage::getDesign()->getSkinUrl($image);
        }
        return $image;
    }

    public function getAttributeThumbs() {
        $images = Mage::getResourceModel('eav/entity_attribute_option')->getAttributeThumbs();
        return $images;
    }
    
}
