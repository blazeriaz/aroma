<?php
class Bluethink_Ccavenues_Block_Redirect extends Mage_Core_Block_Template
{
	
     protected function _toHtml()
    {
        $ccavenuesModel = Mage::getModel('ccavenues/ccavenues');

   
        $html = '<html><body>';
        $html.= $this->__('You will be redirected to the ccavenue website in a few seconds.');
        $html .= '<form id ="ccavenues_payment" method ="post" action ="'.$ccavenuesModel->getSubmitUrl().'" >';
        foreach ($ccavenuesModel->getCCavenuesValues() as $key=>$value) {
        $html.='<input type="hidden" name="'.$key.'" value="'.$value.'"/>';
        }
        $html .= '</form>';     
        $html.= '<script type="text/javascript">document.getElementById("ccavenues_payment").submit();</script>';
        $html.= '</body></html>';
        return $html;
    }

}