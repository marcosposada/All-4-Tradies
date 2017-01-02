<?php

    class Smithandrowe_Profile_Model_Entity_Apprentice extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
    {
        public function getAllOptions()
        {
            if ($this->_options === null) {
                $this->_options = array();
                $this->_options[] = array(
                    'value' => 0,
                    'label' => 'No'
                );
                $this->_options[] = array(
                    'value' => 1,
                    'label' => 'Yes'
                );

            }

            return $this->_options;
        }
    }