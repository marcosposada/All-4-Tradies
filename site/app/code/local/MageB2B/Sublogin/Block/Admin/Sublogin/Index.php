<?php

class
MageB2B_Sublogin_Block_Admin_Sublogin_Index
extends
Mage_Adminhtml_Block_Widget_Grid_Container
{
public
function
__construct()
{
$this->_controller
=
'admin_sublogin';
$this->_blockGroup
=
'sublogin';
$this->_headerText
=
Mage::helper('sublogin')->__('Sublogins View');
parent::__construct();
$this->_removeButton('add');
}
}
