<?php

    class Smithandrowe_Profile_Model_Entity_TradeYearRegistered extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
    {
        public function getAllOptions()
        {
            if ($this->_options === null) {
                $this->_options = array();
                $this->_options[] = array(
                    'value' => '',
                    'label' => 'Trade Years Registered'
                );
                $this->_options[] = array(
                    'value' => 1,
                    'label' => '<1'
                );
                $this->_options[] = array(
                    'value' => 2,
                    'label' => '1<2'
                );
                $this->_options[] = array(
                    'value' => 3,
                    'label' => '2<5'
                );
                $this->_options[] = array(
                    'value' => 4,
                    'label' => '5<10'
                );
                $this->_options[] = array(
                    'value' => 5,
                    'label' => '10<20'
                );
                $this->_options[] = array(
                    'value' => 6,
                    'label' => '20+ yrs'
                );
            }
            return $this->_options;
        }
    }