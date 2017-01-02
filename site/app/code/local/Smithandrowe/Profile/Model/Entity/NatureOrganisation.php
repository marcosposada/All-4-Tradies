<?php

    class Smithandrowe_Profile_Model_Entity_NatureOrganisation extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
    {
        public function getAllOptions()
        {
            if ($this->_options === null) {
                $this->_options = array();
                $this->_options[] = array(
                    'value' => '',
                    'label' => 'Nature of Organisation'
                );
                $this->_options[] = array(
                    'value' => 1,
                    'label' => 'Sole Trader'
                );
                $this->_options[] = array(
                    'value' => 2,
                    'label' => 'Partnership'
                );
                $this->_options[] = array(
                    'value' => 3,
                    'label' => 'Proprietary Company'
                );
                $this->_options[] = array(
                    'value' => 4,
                    'label' => 'Trust'
                );
            }
            return $this->_options;
        }
    }