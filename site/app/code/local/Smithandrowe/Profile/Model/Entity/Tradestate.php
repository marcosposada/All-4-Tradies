<?php

    class Smithandrowe_Profile_Model_Entity_Tradestate extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
    {
        public function getAllOptions()
        {
            if ($this->_options === null) {
                $this->_options = array();
                $this->_options[] = array(
                    'value' => '',
                    'label' => 'Choose Your Registered State..'
                );
                $this->_options[] = array(
                    'value' => 1,
                    'label' => 'ACT'
                );
                $this->_options[] = array(
                    'value' => 2,
                    'label' => 'NSW'
                );
                $this->_options[] = array(
                    'value' => 3,
                    'label' => 'NT'
                );
                $this->_options[] = array(
                    'value' => 4,
                    'label' => 'QLD'
                );
                $this->_options[] = array(
                    'value' => 5,
                    'label' => 'SA'
                );
                $this->_options[] = array(
                    'value' => 6,
                    'label' => 'TAS'
                );
                $this->_options[] = array(
                    'value' => 7,
                    'label' => 'VIC'
                );
                $this->_options[] = array(
                    'value' => 8,
                    'label' => 'WA'
                );
            }

            return $this->_options;
        }
    }