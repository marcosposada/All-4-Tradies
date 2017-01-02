<?php

class
MageB2B_Sublogin_SubloginController
extends
Mage_Adminhtml_Controller_Action
{
protected
function
_initAction()
{
$this->loadLayout()
->_setActiveMenu('sublogin/sublogin')
->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'),
Mage::helper('adminhtml')->__('Sublogin Manager'));
return
$this;
}
public
function
indexAction()
{
$this->_initAction();
$this->_addContent($this->getLayout()->createBlock('sublogin/admin_sublogin_index'));
$this->renderLayout();
}
}
