<?php

class Smithandrowe_PriceToolbar_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $set_gst = $this->getRequest()->getParam('gst');
        if (!empty($set_gst) && $set_gst == 'on') {
            Mage::getSingleton('core/session')->setPriceIncGST(1);
        } else {
            Mage::getSingleton('core/session')->setPriceIncGST(0);
        }

        $referer_url = $this->_getRefererUrl();
        $this->getResponse()->setRedirect($referer_url);
        return $this;
    }
}
