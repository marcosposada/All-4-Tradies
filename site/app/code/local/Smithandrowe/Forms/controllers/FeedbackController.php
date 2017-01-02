<?php

    class Smithandrowe_Forms_FeedbackController extends Mage_Core_Controller_Front_Action
    {
        public function indexAction()
        {
            $this->loadLayout();
            $block = $this->getLayout()->createBlock(
                'Mage_Core_Block_Template',
                'smithandrowe.feedback',
                array(
                    'template' => 'smithandrowe/forms/feedback.phtml'
                )
            );
            $this->getLayout()->getBlock('content')->append($block);
            $this->_initLayoutMessages('core/session');

            $this->renderLayout();
        }

        public function sendemailAction()
        {
            $params = $this->getRequest()->getParams();
            //Check if hidden field has been entereted, if so, consider it spam and don't send email
            if (!empty($params['realSend'])) {
                //Do nothing
            } //Check if the form submitted is feedback form
            else {
                //Get the template id & variables
                $templateId = "Feedback";
                $emailTemplate = Mage::getModel('core/email_template')->loadByCode($templateId);
                $vars = array('customer_name' => $params['your_name'], 'email_address' => $params['email'], 'telephone' => $params['telephone'], 'comments' => $params['comment']);

                //Get neccessary variables to send email
                $receiveEmail = Mage::getStoreConfig('trans_email/supplier_enquiry/email');
                $receiveName = Mage::getStoreConfig('trans_email/supplier_enquiry/name');
                $emailTemplate->setSenderEmail($receiveEmail);
                $emailTemplate->setSenderName($receiveName);

                try {
                    $emailTemplate->send($receiveEmail, $receiveName, $vars);
                    Mage::getSingleton('core/session')->addSuccess('Thank you for your enquiry. One of our team members will be in contact with you within the next 24-48hrs.');
                }
                catch (Exception $e)
                {
                    Mage::getSingleton('core/sesson')->addError('Error sending mail');
                }
                $this->_redirect('feedback.html');
            }
        }

    }