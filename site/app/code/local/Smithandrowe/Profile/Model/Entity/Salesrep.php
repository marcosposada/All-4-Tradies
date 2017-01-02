<?php

    class TRA_Profile_Model_Entity_SalesRep extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
    {
        public function getAllOptions()
        {
            if ($this->_options === null) {
                $this->_options = array();
                $this->_options[] = array('value' => '', 'label' => 'Choose a Sales Representative..');
                $this->_options[] = array('value' => 1, 'label' => 'Andrew Comans');
                $this->_options[] = array('value' => 2, 'label' => 'Phil Wade');
                $this->_options[] = array('value' => 3, 'label' => 'Matt Scurrah');
            }
            return $this->_options;
        }
    }