<?php
class Ves_ProductCategories_Model_System_Config_Source_ListCategories {
    static $arr = array();
    static $tmp = array();
    public function getTreeCategories($parentId,$level = 0, $caret = ' _ '){
        $allCats = Mage::getModel('catalog/category')->getCollection()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('is_active','1')
                    ->addAttributeToFilter('include_in_menu','1')
                    ->addAttributeToSort('position', 'asc'); 
                    if ($parentId) {
                        $allCats->addAttributeToFilter('parent_id',array('eq' => $parentId));
                    }
                    
                    
                    
        $prefix = "";
        if($level) {
            for($i=0;$i < $level; $i++) {
                $prefix .= $caret;
            }
        }
        foreach($allCats as $category)
        {
                $tmp["value"] = $category->getId();
                $tmp["label"] = '|.'.$prefix.$category->getName();
                $arr[] = $tmp;
            $subcats = $category->getChildren();
            if($subcats != ''){ 
                $arr = array_merge($arr,$this-> getTreeCategories($category->getId(),(int)$level + 1, $caret.' _ '));
            }
            
        }
        return $arr;
    }

    public function toOptionArray() {
        //$rootcatId = Mage::app()->getWebsite(true)->getDefaultStore()->getRootCategoryId();
        return $this->getTreeCategories("",0);
    }

}