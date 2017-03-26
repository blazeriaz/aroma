<?php

class Solwin_AttributeImage_Block_Catalog_Product_Attribute_Edit_Tab_Options extends Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Options
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('solwin/catalog/product/attribute/options.phtml');
    }
	
		public function getdescription($option_id){
    //Get the resource model
    $resource = Mage::getSingleton('core/resource');

    //Retrieve the read connection
    $readConnection = $resource->getConnection('core_read');

    //Retrieve our table name
    $table = $resource->getTableName('eav/attribute_option');
    $query = 'SELECT description FROM ' . $table . ' WHERE option_id = '
    . (int)$option_id . ' LIMIT 1';

    //Execute the query and store the result
    $imgUrl = $readConnection->fetchOne($query);
    return $imgUrl;
	} 
	
		public function getParams($option_id){
    //Get the resource model
    $resource = Mage::getSingleton('core/resource');

    //Retrieve the read connection
    $readConnection = $resource->getConnection('core_read');

    //Retrieve our table name
    $table = $resource->getTableName('eav/attribute_option');
    $query = 'SELECT params FROM ' . $table . ' WHERE option_id = '
    . (int)$option_id . ' LIMIT 1';

    //Execute the query and store the result
    $imgUrl = $readConnection->fetchOne($query);
    return $imgUrl;
	} 

    public function getOptionValues()
    {
        $attributeType = $this->getAttributeObject()->getFrontendInput();
        $defaultValues = $this->getAttributeObject()->getDefaultValue();
        if ($attributeType == 'select' || $attributeType == 'multiselect') {
            $defaultValues = explode(',', $defaultValues);
        } else {
            $defaultValues = array();
        }

        switch ($attributeType) {
            case 'select':
                $inputType = 'radio';
                break;
            case 'multiselect':
                $inputType = 'checkbox';
                break;
            default:
                $inputType = '';
                break;
        }

        $values = $this->getData('option_values');
        if (is_null($values)) {
            $values = array();
            $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($this->getAttributeObject()->getId())
                ->setPositionOrder('desc', true)
                ->load();

            foreach ($optionCollection as $option) {
                $value = array();
                if (in_array($option->getId(), $defaultValues)) {
                    $value['checked'] = 'checked="checked"';
                } else {
                    $value['checked'] = '';
                }

                $value['intype'] = $inputType;
                $value['id'] = $option->getId();
                $value['sort_order'] = $option->getSortOrder();
                $value['image'] = $option->getImage();
                $value['thumb'] = $option->getThumb();
                $value['description'] = $option->getDescription();
                $value['params'] = $option->getParams();
                foreach ($this->getStores() as $store) {
                    $storeValues = $this->getStoreOptionValues($store->getId());
                    if (isset($storeValues[$option->getId()])) {
                        $value['store'.$store->getId()] = htmlspecialchars($storeValues[$option->getId()]);
                    }
                    else {
                        $value['store'.$store->getId()] = '';
                    }
                }
                $values[] = new Varien_Object($value);
            }
            $this->setData('option_values', $values);
        }

        return $values;
    }
}