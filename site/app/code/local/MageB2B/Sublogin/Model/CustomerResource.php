<?php


class
MageB2B_Sublogin_Model_CustomerResource
extends
Mage_Customer_Model_Resource_Customer
{

public
function
saveAttribute(Varien_Object
$_cf018b51955567f3c86a7155853c4229480c4bd6,
$_cb342167c458839a3bb75867845dc6ce2415a0b6)
{


if
(in_array($_cb342167c458839a3bb75867845dc6ce2415a0b6,
array('password_hash',
'rp_token',
'rp_token_created_at')))
{
$_930a0745b391fca7316377cbe31f8f0a4441b9d3
=
Mage::app()->getRequest();
$_3a69ee1fffa7b37ae32c13e48dfd5890e9ca68d9
=
(bool)($_930a0745b391fca7316377cbe31f8f0a4441b9d3->getControllerName().'_'.$_930a0745b391fca7316377cbe31f8f0a4441b9d3->getActionName()
==
'account_forgotpasswordpost');
$_aa0d150c512db0683d9c50c0c12f71b1d80409a0
=
(bool)($_930a0745b391fca7316377cbe31f8f0a4441b9d3->getControllerName().'_'.$_930a0745b391fca7316377cbe31f8f0a4441b9d3->getActionName()
==
'account_resetpasswordpost');
if
($_3a69ee1fffa7b37ae32c13e48dfd5890e9ca68d9
||
$_aa0d150c512db0683d9c50c0c12f71b1d80409a0)
{
$_cf018b51955567f3c86a7155853c4229480c4bd6->save();
$_cf018b51955567f3c86a7155853c4229480c4bd6
=
$_cf018b51955567f3c86a7155853c4229480c4bd6->loadByEmail($_930a0745b391fca7316377cbe31f8f0a4441b9d3->getParam('email'));
$_cf018b51955567f3c86a7155853c4229480c4bd6->setEmail($_930a0745b391fca7316377cbe31f8f0a4441b9d3->getParam('email'));
return;
}
}
return
parent::saveAttribute($_cf018b51955567f3c86a7155853c4229480c4bd6,
$_cb342167c458839a3bb75867845dc6ce2415a0b6);
}

public
function
loadByEmail(Mage_Customer_Model_Customer
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c,
$_ff0a3525eb1a63ce44e7951a0b522829a4d2f607,
$_f970317d6ba1fcd2cb9e02bbb6f967c922e4e5ca
=
false)
{
$_39cc59a647aed4446ab8f3ef32b7135f664a845f
=
Mage::getModel('sublogin/sublogin')->getCollection()->addFieldToFilter('email',
$_ff0a3525eb1a63ce44e7951a0b522829a4d2f607);




$_ecd7e55877f28a8e3e14ca46c40ecf420dcdb9a6
=
Mage::app()->getRequest()->getPost('original_login');
if
(strpos($_ff0a3525eb1a63ce44e7951a0b522829a4d2f607,
"@")
===
false)
{
$_ecd7e55877f28a8e3e14ca46c40ecf420dcdb9a6
=
array('username'=>$_ff0a3525eb1a63ce44e7951a0b522829a4d2f607);
}
if
(isset($_ecd7e55877f28a8e3e14ca46c40ecf420dcdb9a6['username']))
{
$_39cc59a647aed4446ab8f3ef32b7135f664a845f
=
Mage::getModel('sublogin/sublogin')->getCollection()->addFieldToFilter('customer_id',
$_ecd7e55877f28a8e3e14ca46c40ecf420dcdb9a6['username']);
}

$_930a0745b391fca7316377cbe31f8f0a4441b9d3
=
Mage::app()->getRequest();
$_c50c3755974ed43c1eb96eb5e083f81b281339d0
=
(bool)($_930a0745b391fca7316377cbe31f8f0a4441b9d3->getControllerName().'_'.$_930a0745b391fca7316377cbe31f8f0a4441b9d3->getActionName()
==
'account_loginPost');
$_3a69ee1fffa7b37ae32c13e48dfd5890e9ca68d9
=
(bool)($_930a0745b391fca7316377cbe31f8f0a4441b9d3->getControllerName().'_'.$_930a0745b391fca7316377cbe31f8f0a4441b9d3->getActionName()
==
'account_forgotpasswordpost');
$_c6eb4ae67d924180789f177a814467ccccab6939
=
(bool)($_930a0745b391fca7316377cbe31f8f0a4441b9d3->getControllerName().'_'.$_930a0745b391fca7316377cbe31f8f0a4441b9d3->getActionName()
==
'account_resetpassword');
$_aa0d150c512db0683d9c50c0c12f71b1d80409a0
=
(bool)($_930a0745b391fca7316377cbe31f8f0a4441b9d3->getControllerName().'_'.$_930a0745b391fca7316377cbe31f8f0a4441b9d3->getActionName()
==
'account_resetpasswordpost');
if
($_c50c3755974ed43c1eb96eb5e083f81b281339d0
||
$_3a69ee1fffa7b37ae32c13e48dfd5890e9ca68d9
||
$_c6eb4ae67d924180789f177a814467ccccab6939
||
$_aa0d150c512db0683d9c50c0c12f71b1d80409a0)
{
$_8c21f819efc249c0efe711aa784aa8a6a6d8a13f
=
Mage::app()->getWebsite()->getWebsiteId();
foreach
($_39cc59a647aed4446ab8f3ef32b7135f664a845f
as
$_4e45fa4f3249d9c3cc1314ed89922593008cf117)
{

$_f9712cf3eb2bf043e1ddede22c150b1170c69142
=
Mage::getModel('customer/customer')->load($_4e45fa4f3249d9c3cc1314ed89922593008cf117->getEntityId());
if
($_f9712cf3eb2bf043e1ddede22c150b1170c69142->getWebsiteId()
!=
$_8c21f819efc249c0efe711aa784aa8a6a6d8a13f)
continue;
if
($_4e45fa4f3249d9c3cc1314ed89922593008cf117->getId())
{

if
($_4e45fa4f3249d9c3cc1314ed89922593008cf117->getActive())
{
return
$this->_loginSublogin($_4e45fa4f3249d9c3cc1314ed89922593008cf117,
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c);
}

else
{
if
($_3a69ee1fffa7b37ae32c13e48dfd5890e9ca68d9)
{
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->setExpireDate(strtotime(date("Y-m-d",
strtotime(date('Y-m-d')))
.
" +90 days"));
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->setActive(1);
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->save();
return
$this->_loginSublogin($_4e45fa4f3249d9c3cc1314ed89922593008cf117,
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c);
}
else
{
Mage::helper('sublogin')->sendAccountExpiredMail($_4e45fa4f3249d9c3cc1314ed89922593008cf117);
Mage::getSingleton('customer/session')->addError(Mage::helper('sublogin')->__("Your account is deactivated. You have received an email to reactivate the account."));

throw
new
Exception(Mage::helper('sublogin')->__("Your account is deactivated. You have received an email to reactivate the account."));
}
}
}
}
}

Mage::getSingleton('customer/session')->setSubloginEmail(false);
return
parent::loadByEmail($_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c,
$_ff0a3525eb1a63ce44e7951a0b522829a4d2f607,
$_f970317d6ba1fcd2cb9e02bbb6f967c922e4e5ca);
}

protected
function
_loginSublogin($_4e45fa4f3249d9c3cc1314ed89922593008cf117,
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c)
{
Mage::getSingleton('customer/session')->setSubloginEmail($_4e45fa4f3249d9c3cc1314ed89922593008cf117->getEmail());

$this->load($_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c,
(int)$_4e45fa4f3249d9c3cc1314ed89922593008cf117->getEntityId());
Mage::helper('sublogin')->setFrontendLoadAttributes($_4e45fa4f3249d9c3cc1314ed89922593008cf117,
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c);
return
$this;
}
}
