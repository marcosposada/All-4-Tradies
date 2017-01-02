<?php


    class Biztech_Auspost_Block_Adminhtml_Config_Form_Renderer_Website extends Mage_Adminhtml_Block_System_Config_Form_Field
    {

        protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
        {

            $html = '';
            $websiteId = Mage::app()->getRequest()->getParam('website');
            $data = Mage::app()->getWebsite($websiteId)->getConfig('auspost/activation/data');
            $ele_value = explode(',', str_replace($data, '', Mage::helper('core')->decrypt($element->getValue())));
            $ele_name = $element->getName();
            $ele_id = $element->getId();
            $element->setName($ele_name . '[]');
            $data_info = Mage::helper('auspost')->getDataInfo();
            if(isset($data_info['dom']) ){
                foreach (Mage::app()->getWebsites() as $website) {
                    $element->setChecked(false);
                    $id = $website->getId();
                    $name = $website->getName();
                    $element->setId($ele_id.'_'.$id);
                    $element->setValue($id);
                    if(in_array($id, $ele_value) !== false){
                        $element->setChecked(true);
                    }

                    if ($id!=0) {
                        $html .= '<div><label>'.$element->getElementHtml().' '.$name.' </label></div>';
                    }
                }
            }else{
                $html = sprintf('<strong class="required">%s</strong>', $this->__('Please enter a valid key'));
            }
            return $html;
        }
}