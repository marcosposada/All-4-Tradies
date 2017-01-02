<?php

    class Smithandrowe_Profile_Model_Entity_Trade extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
    {
        public function getAllOptions()
        {
            if ($this->_options === null) {
                $this->_options = array();
                $this->_options[] = array(
                    'value' => '',
                    'label' => 'Choose Your Trade'
                );
                $this->_options[] = array(
                    'value' => 1,
                    'label' => 'Air Conditioning Installer'
                );
                $this->_options[] = array(
                    'value' => 2,
                    'label' => 'Architect'
                );

                $this->_options[] = array(
                    'value' => 4,
                    'label' => 'Bricklayer'
                );
                $this->_options[] = array(
                    'value' => 5,
                    'label' => 'Builder'
                );
                $this->_options[] = array(
                    'value' => 34,
                    'label' => 'Building Consultant'
                );
                //
                $this->_options[] = array(
                    'value' => 6,
                    'label' => 'Cabinet Maker'
                );

                $this->_options[] = array(
                    'value' => 7,
                    'label' => 'Carpenter / Chippy'
                );
                $this->_options[] = array(
                    'value' => 8,
                    'label' => 'Decorator'
                );
                $this->_options[] = array(
                    'value' => 9,
                    'label' => 'Demolisher'
                );

                $this->_options[] = array(
                    'value' => 10,
                    'label' => 'Disconnection and reconnection of fixed electrical equipment'
                );
                $this->_options[] = array(
                    'value' => 11,
                    'label' => 'Dry Plasterer'
                );



                $this->_options[] = array(
                    'value' => 13,
                    'label' => 'Electrician / Sparkie'
                );
                $this->_options[] = array(
                    'value' => 12,
                    'label' => 'Erection of Prefabricated Metal framed home additions & structures '
                );
                $this->_options[] = array(
                    'value' => 14,
                    'label' => 'Home Renovator Builder'
                );
                $this->_options[] = array(
                    'value' => 15,
                    'label' => 'Excavator'
                );

                $this->_options[] = array(
                    'value' => 16,
                    'label' => 'Fencer'
                );
                $this->_options[] = array(
                    'value' => 17,
                    'label' => 'Floorer'
                );
                $this->_options[] = array(
                    'value' => 18,
                    'label' => 'Concretor - General'
                );


                $this->_options[] = array(
                    'value' => 19,
                    'label' => 'Glazer'
                );
                $this->_options[] = array(
                    'value' => 20,
                    'label' => 'Installer (security doors, grilles & equipment) '
                );

                //
                $this->_options[] = array(
                    'value' => 21,
                    'label' => 'Joiner'
                );

                $this->_options[] = array(
                    'value' => 22,
                    'label' => 'Kitchen Builder'
                );
                $this->_options[] = array(
                    'value' => 23,
                    'label' => 'Mechanic'
                );
                $this->_options[] = array(
                    'value' => 24,
                    'label' => 'Metal Fabricator'
                );


                $this->_options[] = array(
                    'value' => 25,
                    'label' => 'Maintenance & Cleaning'
                );
                $this->_options[] = array(
                    'value' => 26,
                    'label' => 'Minor Trade Work'
                );
                $this->_options[] = array(
                    'value' => 27,
                    'label' => 'Painter'
                );

                $this->_options[] = array(
                    'value' => 28,
                    'label' => 'Plumbier (Draining & Gasfitting)'
                );
                $this->_options[] = array(
                    'value' => 29,
                    'label' => 'Roof Plumber'
                );
                $this->_options[] = array(
                    'value' => 30,
                    'label' => 'Roofer Slating'
                );


                $this->_options[] = array(
                    'value' => 31,
                    'label' => 'Roofer Tiling'
                );
                $this->_options[] = array(
                    'value' => 32,
                    'label' => 'Stonemason'
                );
                $this->_options[] = array(
                    'value' => 33,
                    'label' => 'Structural Landscaper'
                );

                $this->_options[] = array(
                    'value' => 3,
                    'label' => 'Swimming Pool Builder'
                );
                $this->_options[] = array(
                    'value' => 35,
                    'label' => 'Underpinning Piering'
                );
                $this->_options[] = array(
                    'value' => 36,
                    'label' => 'Tiler'
                );
                $this->_options[] = array(
                    'value' => 37,
                    'label' => 'Waterproofer'
                );
                $this->_options[] = array(
                    'value' => 38,
                    'label' => 'Wet Plasterer'
                );
                $this->_options[] = array(
                    'value' => 'other',
                    'label' => 'Other'
                );

            }

            return $this->_options;
        }
    }