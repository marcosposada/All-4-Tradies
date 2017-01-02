<?php

    class Smithandrowe_Profile_Model_Entity_AccountType extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
    {
        public function getAllOptions()
        {
            if ($this->_options === null) {
                $this->_options = array();
                $this->_options[] = array(
                    'value' => '',
                    'label' => 'Type of Account'
                );
                $this->_options[] = array(
                    'value' => 1,
                    'label' => 'DIY'
                );
                $this->_options[] = array(
                    'value' => 2,
                    'label' => 'Tradie - Business'
                );
                $this->_options[] = array(
                    'value' => 3,
                    'label' => 'Tradie - Sole'
                );
                $this->_options[] = array(
                    'value' => 4,
                    'label' => 'Corporate'
                );
                $this->_options[] = array(
                    'value' => 5,
                    'label' => 'Staff'
                );
                $this->_options[] = array(
                    'value' => 6,
                    'label' => 'Family/Friend'
                );
            }

            return $this->_options;
        }
    }