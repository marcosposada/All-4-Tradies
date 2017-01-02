<?php

    class Smithandrowe_Profile_Model_Entity_Age extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
    {
        public function getAllOptions()
        {
            if ($this->_options === null) {
                $this->_options = array();
                $this->_options[] = array(
                    'value' => '',
                    'label' => 'Choose Your Age..'
                );
                $this->_options[] = array(
                    'value' => 1,
                    'label' => '16-24'
                );
                $this->_options[] = array(
                    'value' => 1,
                    'label' => '25-34'
                );
                $this->_options[] = array(
                    'value' => 1,
                    'label' => '35-44'
                );
                $this->_options[] = array(
                    'value' => 1,
                    'label' => '45-54'
                );
                $this->_options[] = array(
                    'value' => 1,
                    'label' => '55+'
                );
            }

            return $this->_options;
        }
    }