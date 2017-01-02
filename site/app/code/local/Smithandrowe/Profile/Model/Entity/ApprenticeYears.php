<?php

    class Smithandrowe_Profile_Model_Entity_ApprenticeYears extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
    {
        public function getAllOptions()
        {
            if ($this->_options === null) {
                $this->_options = array();
                $this->_options[] = array(
                    'value' => '',
                    'label' => 'Apprentice Year'
                );
                $this->_options[] = array(
                    'value' => 1,
                    'label' => '1'
                );
                $this->_options[] = array(
                    'value' => 2,
                    'label' => '2'
                );
                $this->_options[] = array(
                    'value' => 3,
                    'label' => '3'
                );
                $this->_options[] = array(
                    'value' => 4,
                    'label' => '4'
                );
                $this->_options[] = array(
                    'value' => 5,
                    'label' => '5'
                );
            }
            return $this->_options;
        }
    }