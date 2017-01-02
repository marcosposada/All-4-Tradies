<?php

    class Smithandrowe_Profile_Model_Entity_Occupation extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
    {
        public function getAllOptions()
        {
            if ($this->_options === null) {
                $this->_options = array();
                $this->_options[] = array(
                    'value' => '',
                    'label' => ''
                );
                $this->_options[] = array(
                    'value' => 1,
                    'label' => 'Accounting'
                );
                $this->_options[] = array(
                    'value' => 2,
                    'label' => 'Administration & Office Support'
                );
                $this->_options[] = array(
                    'value' => 3,
                    'label' => 'Advertising, Arts & Media'
                );
                $this->_options[] = array(
                    'value' => 4,
                    'label' => 'Banking & Financial Services'
                );
                $this->_options[] = array(
                    'value' => 5,
                    'label' => 'Call Centre & Customer Service'
                );
                $this->_options[] = array(
                    'value' => 6,
                    'label' => 'CEO & General Management'
                );
                $this->_options[] = array(
                    'value' => 7,
                    'label' => 'Community Service & Development'
                );
                $this->_options[] = array(
                    'value' => 8,
                    'label' => 'Construction'
                );
                $this->_options[] = array(
                    'value' => 9,
                    'label' => 'Consulting & Strategy'
                );
                $this->_options[] = array(
                    'value' => 10,
                    'label' => 'Design & Architecture'
                );

                $this->_options[] = array(
                    'value' => 11,
                    'label' => 'Education & Training'
                );
                $this->_options[] = array(
                    'value' => 12,
                    'label' => 'Engineering'
                );
                $this->_options[] = array(
                    'value' => 13,
                    'label' => 'Farming, Animals & Conservation'
                );
                $this->_options[] = array(
                    'value' => 14,
                    'label' => 'Government & Defence'
                );
                $this->_options[] = array(
                    'value' => 15,
                    'label' => 'Healthcare & Medical'
                );
                $this->_options[] = array(
                    'value' => 16,
                    'label' => 'Hospitality & Tourism'
                );
                $this->_options[] = array(
                    'value' => 17,
                    'label' => 'Human Resources & Recruitment'
                );
                $this->_options[] = array(
                    'value' => 18,
                    'label' => 'Information & Communication Technology'
                );
                $this->_options[] = array(
                    'value' => 19,
                    'label' => 'Insurance & Superannuation'
                );
                $this->_options[] = array(
                    'value' => 20,
                    'label' => 'Legal'
                );

                $this->_options[] = array(
                    'value' => 21,
                    'label' => 'Manufacturing, Transport & Logistics'
                );
                $this->_options[] = array(
                    'value' => 22,
                    'label' => 'Marketing & Communications'
                );
                $this->_options[] = array(
                    'value' => 23,
                    'label' => 'Mining, Resources & Energy'
                );
                $this->_options[] = array(
                    'value' => 24,
                    'label' => 'Real Estate & Property'
                );
                $this->_options[] = array(
                    'value' => 25,
                    'label' => 'Retail & Consumer Products'
                );
                $this->_options[] = array(
                    'value' => 26,
                    'label' => 'Sales'
                );
                $this->_options[] = array(
                    'value' => 27,
                    'label' => 'Science & Technology'
                );
                $this->_options[] = array(
                    'value' => 28,
                    'label' => 'Self Employed'
                );
                $this->_options[] = array(
                    'value' => 29,
                    'label' => 'Sport & Recreation'
                );
                $this->_options[] = array(
                    'value' => 30,
                    'label' => 'Trades & Services'
                );
            }
            return $this->_options;
        }
    }