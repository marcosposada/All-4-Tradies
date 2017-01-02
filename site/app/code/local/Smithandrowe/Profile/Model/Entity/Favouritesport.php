<?php

    class Smithandrowe_Profile_Model_Entity_FavouriteSport extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
    {
        public function getAllOptions()
        {
            if ($this->_options === null) {
                $this->_options = array();
                $this->_options[] = array(
                    'value' => '',
                    'label' => 'Choose Your Favourite Sport..'
                );
                $this->_options[] = array(
                    'value' => 1,
                    'label' => 'AFL'
                );
                $this->_options[] = array(
                    'value' => 2,
                    'label' => 'Cricket'
                );
                $this->_options[] = array(
                    'value' => 3,
                    'label' => 'EPL'
                );
                $this->_options[] = array(
                    'value' => 4,
                    'label' => 'Formula 1'
                );
                $this->_options[] = array(
                    'value' => 5,
                    'label' => 'Golf'
                );
                $this->_options[] = array(
                    'value' => 6,
                    'label' => 'MLB'
                );
                $this->_options[] = array(
                    'value' => 7,
                    'label' => 'MotoGP'
                );
                $this->_options[] = array(
                    'value' => 8,
                    'label' => 'NBA'
                );
                $this->_options[] = array(
                    'value' => 9,
                    'label' => 'NBL'
                );
                $this->_options[] = array(
                    'value' => 10,
                    'label' => 'NFL'
                );
                $this->_options[] = array(
                    'value' => 11,
                    'label' => 'NHL'
                );
                $this->_options[] = array(
                    'value' => 12,
                    'label' => 'NRL'
                );
                $this->_options[] = array(
                    'value' => 13,
                    'label' => 'Soccer'
                );
                $this->_options[] = array(
                    'value' => 14,
                    'label' => 'Tennis'
                );
                $this->_options[] = array(
                    'value' => 15,
                    'label' => 'V8 Supercars'
                );
                $this->_options[] = array(
                    'value' => 16,
                    'label' => 'WNBL'
                );
            }

            return $this->_options;
        }
    }