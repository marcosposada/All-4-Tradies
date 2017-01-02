<?php

    class Smithandrowe_Profile_Model_Entity_Interests extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
    {
        public function getAllOptions()
        {
            if ($this->_options === null) {
                $this->_options = array();
                $this->_options[] = array(
                    'value' => '',
                    'label' => 'Choose Your Interests'
                );
                $this->_options[] = array(
                    'value' => 1,
                    'label' => 'Landscaping'
                );
                $this->_options[] = array(
                    'value' => 2,
                    'label' => 'Electrical'
                );
                $this->_options[] = array(
                    'value' => 3,
                    'label' => 'Plumbing'
                );
                $this->_options[] = array(
                    'value' => 4,
                    'label' => 'Building'
                );
            }

            return $this->_options;
        }
    }