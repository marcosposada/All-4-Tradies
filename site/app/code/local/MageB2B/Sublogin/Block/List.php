<?php

class
MageB2B_Sublogin_Block_List
extends
Mage_Customer_Block_Account_Dashboard
{
protected
$_collection;
protected
function
_construct()
{
$this->_collection
=
Mage::getModel('sublogin/sublogin')->getCollection();
$_846d2db48108a95ac7c54994744716179bf69a80
=
Mage::getSingleton('customer/session')->getCustomer()->getId();
$this->_collection->addFieldToFilter('entity_id',array('eq'=>$_846d2db48108a95ac7c54994744716179bf69a80));
}
protected
function
_getCollection()
{
return
$this->_collection;
}
public
function
getCollection()
{
return
$this->_getCollection();
}
protected
function
_prepareLayout()
{
return
parent::_prepareLayout();
}
protected
function
_beforeToHtml()
{
return
parent::_beforeToHtml();
}
public
function
count()
{
return
$this->_collection->getSize();
}
public
function
getSortlink($_3f69c14e858b5c1df34d113331775fb073451b81,
$_3c234514969b97ab3afdf6028bbc1c46a494841f)
{
$_eb03ffe1dd98957980d0e5008d2a33cefda56ec3
=
'desc';
$_11d9530e1024e0d869f1e86f8e94f301f6ea2ef2
=
'not-sort';
if
($_3f69c14e858b5c1df34d113331775fb073451b81
==
$this->getRequest()->getParam('sort'))
{
$_eb03ffe1dd98957980d0e5008d2a33cefda56ec3
=
$this->getRequest()->getParam('direction')=='asc'?'desc':'asc';
$_11d9530e1024e0d869f1e86f8e94f301f6ea2ef2
=
'sort-arrow-'.(($this->getRequest()->getParam('direction')=='asc')?'asc':'desc');
}
return
'<a class="'.$_11d9530e1024e0d869f1e86f8e94f301f6ea2ef2.'" href="'.Mage::getUrl('sublogin/frontend/index/',
array('sort'=>$_3f69c14e858b5c1df34d113331775fb073451b81,
'direction'=>$_eb03ffe1dd98957980d0e5008d2a33cefda56ec3)).'">'.
'<span class="sort-title">'.$_3c234514969b97ab3afdf6028bbc1c46a494841f.'</span></a>';
}
public
function
dateFormat($_f0c57a04a51d1d900c8d28a111ab8f340a17465b)
{
if
(!$_f0c57a04a51d1d900c8d28a111ab8f340a17465b)
return
'';
return
$this->formatDate($_f0c57a04a51d1d900c8d28a111ab8f340a17465b,
Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
}
}
