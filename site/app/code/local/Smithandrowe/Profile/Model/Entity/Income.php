<?php

    class Smithandrowe_Profile_Model_Entity_Income extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
    {
        public function getAllOptions()
        {
            if ($this->_options === null) {
                $this->_options = array();
                $this->_options[] = array(
                    'value' => '',
                    'label' => 'Income'
                );
                $this->_options[] = array(
                    'value' => 1,
                    'label' => '< $50,000'
                );
                $this->_options[] = array(
                    'value' => 2,
                    'label' => '$50,000 < $75,000'
                );
                $this->_options[] = array(
                    'value' => 3,
                    'label' => '$75,000 < $100,000'
                );
                $this->_options[] = array(
                    'value' => 4,
                    'label' => '$100,000 < $125,000'
                );
                $this->_options[] = array(
                    'value' => 5,
                    'label' => '$125,000 < $150,000'
                );
            }
            return $this->_options;
        }
    }