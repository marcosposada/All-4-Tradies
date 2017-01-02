<?php
    class Smithandrowe_Forms_IndexController extends Mage_Core_Controller_Front_Action
    {
        public function indeAction()
        {
            Mage::log('Have index');
            $this->loadLayout();
            $block = $this->getLayout()->createBlock(
                'Mage_Core_Block_Template',
                'smithandrowe.feedback',
                array(
                    'template' => 'smithandrowe/forms/feedback.phtml'
                )
            );
            $this->getLayout()->getBlock('content')->append($block);
            //$this->getLayout()->getBlock('right')->insert($block, 'catalog.compare.sidebar', true);
            $this->_initLayoutMessages('core/session');

            $this->renderLayout();
        }

        public function sendemailAction() {
            $params = $this->getRequest()->getParams();
            //Check if hidden field has been entereted, if so, consider it spam and don't send email
            if (!empty($params['realSend'])) {
                //Do nothing
            }
            //Check if the form submitted is feedback form
            elseif ($params['type'] == 'feedback') {

                //Get the template id & variables
            $templateId = "Feedback";
            $emailTemplate = Mage::getModel('core/email_template')->loadByCode($templateId);
            $vars = array('customer_name' => $params['your_name'], 'email_address' => $params['email'], 'telephone' => $params['telephone'], 'comments' => $params['comment']);

                //Get neccessary variables to send email
                $receiveEmail = Mage::getStoreConfig('trans_email/supplier_enquiry/email');
                $receiveName = Mage::getStoreConfig('trans_email/supplier_enquiry/name');
            $emailTemplate->setSenderEmail($receiveEmail);
            $emailTemplate->setSenderName($receiveName);


            $emailTemplate->send($receiveEmail,$receiveName, $vars);
            }
            elseif ($params['type'] == 'supplier_enquiry') {

            }
        }

    }