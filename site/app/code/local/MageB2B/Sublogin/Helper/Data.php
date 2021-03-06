<?php

class
MageB2B_Sublogin_Helper_Data
extends
Mage_Core_Helper_Abstract
{

public
function
getCurrentSublogin(Mage_Customer_Model_Customer
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c
=
null,
$_ff0a3525eb1a63ce44e7951a0b522829a4d2f607
=
'')
{
$_00bfc7a78eb28c3a15ee3f0a828d5a0f3f8d9b1d
=
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c
?
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getId()
:
Mage::getSingleton('customer/session')->getCustomerId();
$_ff0a3525eb1a63ce44e7951a0b522829a4d2f607
=
$_ff0a3525eb1a63ce44e7951a0b522829a4d2f607?
$_ff0a3525eb1a63ce44e7951a0b522829a4d2f607
:
Mage::getSingleton('customer/session')->getSubloginEmail();
if
($_ff0a3525eb1a63ce44e7951a0b522829a4d2f607)
{
static
$_4e45fa4f3249d9c3cc1314ed89922593008cf117
=
array();
if
(!isset($_4e45fa4f3249d9c3cc1314ed89922593008cf117[$_00bfc7a78eb28c3a15ee3f0a828d5a0f3f8d9b1d.'_'.$_ff0a3525eb1a63ce44e7951a0b522829a4d2f607]))
{

$_205d95c001c842f933e3b55fa4e902d5d2fdd0af
=
Mage::getModel('sublogin/sublogin')->getCollection()->addFieldToFilter('email',
$_ff0a3525eb1a63ce44e7951a0b522829a4d2f607);
foreach
($_205d95c001c842f933e3b55fa4e902d5d2fdd0af
as
$_7f11b1962e14e69c8844d0655d561b3e8cd30d5b)
{
if
($_00bfc7a78eb28c3a15ee3f0a828d5a0f3f8d9b1d
==
$_7f11b1962e14e69c8844d0655d561b3e8cd30d5b->getEntityId())
{
$_4e45fa4f3249d9c3cc1314ed89922593008cf117[$_00bfc7a78eb28c3a15ee3f0a828d5a0f3f8d9b1d.'_'.$_ff0a3525eb1a63ce44e7951a0b522829a4d2f607]
=
$_7f11b1962e14e69c8844d0655d561b3e8cd30d5b;
return
$_7f11b1962e14e69c8844d0655d561b3e8cd30d5b;
}
}
}
else
{
return
$_4e45fa4f3249d9c3cc1314ed89922593008cf117[$_00bfc7a78eb28c3a15ee3f0a828d5a0f3f8d9b1d.'_'.$_ff0a3525eb1a63ce44e7951a0b522829a4d2f607];
}
}
return
null;
}

public
function
validateUniqueEmail($_ff0a3525eb1a63ce44e7951a0b522829a4d2f607,
$_8c21f819efc249c0efe711aa784aa8a6a6d8a13f)
{
$_205d95c001c842f933e3b55fa4e902d5d2fdd0af
=
Mage::getModel('sublogin/sublogin')->getCollection()->addFieldToFilter('email',
$_ff0a3525eb1a63ce44e7951a0b522829a4d2f607);

foreach
($_205d95c001c842f933e3b55fa4e902d5d2fdd0af
as
$_4e45fa4f3249d9c3cc1314ed89922593008cf117)
{
$_f9712cf3eb2bf043e1ddede22c150b1170c69142
=
Mage::getModel('customer/customer')->load($_4e45fa4f3249d9c3cc1314ed89922593008cf117->getEntityId());
if
($_f9712cf3eb2bf043e1ddede22c150b1170c69142->getWebsiteId()
==
$_8c21f819efc249c0efe711aa784aa8a6a6d8a13f)
{
return
false;
}
}
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c
=
Mage::getModel('customer/customer')
->setWebsiteId($_8c21f819efc249c0efe711aa784aa8a6a6d8a13f)
->loadByEmail($_ff0a3525eb1a63ce44e7951a0b522829a4d2f607,
Mage::getModel('customer/customer'));
if
($_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getId())
return
false;
return
true;
}

public
function
setFrontendLoadAttributes($_4e45fa4f3249d9c3cc1314ed89922593008cf117,
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c)
{
if
(!$_4e45fa4f3249d9c3cc1314ed89922593008cf117
||
!$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c)
return;
$_461e9bac5c124e13e943d74294b5b3c23e91e59a
=
array();
foreach
(array('firstname',
'lastname',
'password',
'email',
'customer_id',
'rp_token',
'rp_token_created_at')
as
$_cf80d58e34c98ca4787395f86a0a654da3781807)
{
$_dc7f46f519b41520210eda842ff23db60112e8e7
=
$_cf80d58e34c98ca4787395f86a0a654da3781807;
if
($_cf80d58e34c98ca4787395f86a0a654da3781807
==
'password')
$_dc7f46f519b41520210eda842ff23db60112e8e7
=
'password_hash';
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->setData($_dc7f46f519b41520210eda842ff23db60112e8e7,
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->getData($_cf80d58e34c98ca4787395f86a0a654da3781807));
}
}
public
function
sendAccountExpiredMail($_4e45fa4f3249d9c3cc1314ed89922593008cf117)
{
if
(!$_4e45fa4f3249d9c3cc1314ed89922593008cf117->getData('rp_token'))
{
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->setData('rp_token',
mt_rand(0,PHP_INT_MAX));
}
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->save();
$_44f8b790a939f0b9202f65d2e050f16b3cd0fbd6
=
Mage::getSingleton('core/translate');
$_44f8b790a939f0b9202f65d2e050f16b3cd0fbd6->setTranslateInline(false);
$_8015116b3aae056a390c38ae34678b5abed6d252
=
Mage::getModel('core/email_template');
$_8015116b3aae056a390c38ae34678b5abed6d252->setDesignConfig(array('area'
=>
'frontend'))
->sendTransactional(
Mage::getStoreConfig('sublogin/email/expire_refresh'),
Mage::getStoreConfig('contacts/email/sender_email_identity'),
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->getEmail(),
null,
array('sublogin'
=>
$_4e45fa4f3249d9c3cc1314ed89922593008cf117),
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->getStoreId()
);
if
(!$_8015116b3aae056a390c38ae34678b5abed6d252->getSentSuccess())
{
Mage::getSingleton('core/session')->addError(Mage::helper('sublogin')->__('Problem with sending email to %s',
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->getEmail()));
}
}

public
function
sendNewSubloginEmail($_4e45fa4f3249d9c3cc1314ed89922593008cf117)
{
$_6b0361ca199f403b6f7eb781b83d507641544828
=
'sublogin/email/new';
$this->_sendNewEmail($_6b0361ca199f403b6f7eb781b83d507641544828,
$_4e45fa4f3249d9c3cc1314ed89922593008cf117);
}

public
function
sendNewPasswordEmail($_4e45fa4f3249d9c3cc1314ed89922593008cf117)
{
$_6b0361ca199f403b6f7eb781b83d507641544828
=
'sublogin/email/reset_password';
$this->_sendNewEmail($_6b0361ca199f403b6f7eb781b83d507641544828,
$_4e45fa4f3249d9c3cc1314ed89922593008cf117);
}
protected
function
_sendNewEmail($_6b0361ca199f403b6f7eb781b83d507641544828,
$_4e45fa4f3249d9c3cc1314ed89922593008cf117)
{
$_44f8b790a939f0b9202f65d2e050f16b3cd0fbd6
=
Mage::getSingleton('core/translate');
$_44f8b790a939f0b9202f65d2e050f16b3cd0fbd6->setTranslateInline(false);
$_8015116b3aae056a390c38ae34678b5abed6d252
=
Mage::getModel('core/email_template');
$_8015116b3aae056a390c38ae34678b5abed6d252->setDesignConfig(array('area'
=>
'frontend',
'store'=>$_4e45fa4f3249d9c3cc1314ed89922593008cf117->getStoreId()));
$_8015116b3aae056a390c38ae34678b5abed6d252->addBcc(Mage::getStoreConfig('sublogin/email/send_bcc'));
$_8015116b3aae056a390c38ae34678b5abed6d252->sendTransactional(
Mage::getStoreConfig($_6b0361ca199f403b6f7eb781b83d507641544828,
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->getStoreId()),
Mage::getStoreConfig('contacts/email/sender_email_identity',
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->getStoreId()),
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->getEmail(),
null,
array('sublogin'
=>
$_4e45fa4f3249d9c3cc1314ed89922593008cf117),
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->getStoreId()
);
if
(!$_8015116b3aae056a390c38ae34678b5abed6d252->getSentSuccess())
{
Mage::getSingleton('core/session')->addError(Mage::helper('sublogin')->__('Problem with sending sublogin email to %s',
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->getEmail()));
}
}
}
