<?php

class
MageB2B_Sublogin_Block_Customer_Edit_Tab_Sublogin
extends
Mage_Adminhtml_Block_Widget_Form
{
protected
function
_prepareForm()
{
$_fb05a43927cff438da7fdbd15552ff3227bd4b96
=
new
Varien_Data_Form();
$this->setForm($_fb05a43927cff438da7fdbd15552ff3227bd4b96);
$_64af779d81eb451f44e50893e14780ab31bf8125
=
$_fb05a43927cff438da7fdbd15552ff3227bd4b96->addFieldset('sublogin',
array('legend'=>Mage::helper('sublogin')->__('Sublogins')));
$_fb05a43927cff438da7fdbd15552ff3227bd4b96->setHtmlIdPrefix('_sublogin');
$_fb05a43927cff438da7fdbd15552ff3227bd4b96->setFieldNameSuffix('sublogin');
$_64af779d81eb451f44e50893e14780ab31bf8125->addField('sublogins',
'text',
array(
'name'=>'sublogins',
));
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c
=
Mage::registry('current_customer');
$_91a2a43f77d1dc504cd0664864c1dfd4834a8f53
=
Mage::getModel('core/website')->load($_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getWebsiteId());
$_6ab22cf5b83adf50f6320e3b010f11ee77b3b669
=
array();
foreach
($_91a2a43f77d1dc504cd0664864c1dfd4834a8f53->getStoreCollection()
as
$_7ff01efcb6976550e400d525bb2f8f2090febc06)
$_6ab22cf5b83adf50f6320e3b010f11ee77b3b669[$_7ff01efcb6976550e400d525bb2f8f2090febc06->getId()]
=
$_7ff01efcb6976550e400d525bb2f8f2090febc06->getCode();
$_d2389373c4d226ef389a44a6ea431427c26dd7a9
=
array();
$_d2389373c4d226ef389a44a6ea431427c26dd7a9[]
=
array(
'name'=>'store_id',
'label'=>$this->__('Store Id'),
'required'=>false,
'type'=>'select',
'style'=>'width:100px',
'cssclass'=>'',
'options'=>$_6ab22cf5b83adf50f6320e3b010f11ee77b3b669,
);
if
($_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getCustomerId())
{
$_d2389373c4d226ef389a44a6ea431427c26dd7a9[]
=
array(
'name'=>'customer_id',
'label'=>$this->__('Customer Id'),
'required'=>false,
'type'=>'text',
'style'=>'width:100px',
'cssclass'=>'',
'readonly'=>true,
);
}
$_d2389373c4d226ef389a44a6ea431427c26dd7a9[]
=
array(
'name'=>'email',
'label'=>$this->__('Email'),
'required'=>true,
'type'=>'text',
'style'=>'width:150px',
'cssclass'=>'validate-email',
);
$_d2389373c4d226ef389a44a6ea431427c26dd7a9[]
=
array(
'name'=>'send_backendmails',
'label'=>$this->__('Send Mail'),
'required'=>false,
'type'=>'checkbox',
'style'=>'',
'cssclass'=>'',
);
$_d2389373c4d226ef389a44a6ea431427c26dd7a9[]
=
array(
'name'=>'firstname',
'label'=>$this->__('Firstname'),
'required'=>true,
'type'=>'text',
'style'=>'width:100px',
'cssclass'=>'',
);
$_d2389373c4d226ef389a44a6ea431427c26dd7a9[]
=
array(
'name'=>'lastname',
'label'=>$this->__('Lastname'),
'required'=>true,
'type'=>'text',
'style'=>'width:100px',
'cssclass'=>'',
);
$_d2389373c4d226ef389a44a6ea431427c26dd7a9[]
=
array(
'name'=>'password',
'label'=>$this->__('Password'),
'required'=>true,
'type'=>'text',
'style'=>'width:80px',
'cssclass'=>'validate-password',
'onlyNewRequired'=>true,
'onlyNewValue'=>true,
);
$_d2389373c4d226ef389a44a6ea431427c26dd7a9[]
=
array(
'name'=>'days_to_expire',
'label'=>$this->__('Days to Expire'),
'required'=>false,
'type'=>'text',
'style'=>'',
'cssclass'=>'',
);


$_2ecc41b28041667049b648881d5ff17111568699
=
$_fb05a43927cff438da7fdbd15552ff3227bd4b96->getElement('sublogins')->getName();
$_2289f66a254ac0ee1bb00efbd40a49e3e810d1f2
=
$_fb05a43927cff438da7fdbd15552ff3227bd4b96->getElement('sublogins')->getHtmlId();
$_e60edee9bbcec9af2084c09a7e3e3bf5a0643e9f
=
<<<EOH
<div style="width:100px">
                <img style="margin-top:1px;float:right"
                    id="{$_2289f66a254ac0ee1bb00efbd40a49e3e810d1f2}_row_{{index}}_expire_date_trig"
                    src="{$this->getSkinUrl('images/grid-cal.gif')}" />
                <input rel="{{index}}" class="input-text" type="text" value="{{expire_date}}"
                    name="{$_2ecc41b28041667049b648881d5ff17111568699}[{{index}}][expire_date]" id="{$_2289f66a254ac0ee1bb00efbd40a49e3e810d1f2}_row_{{index}}_expire_date"
                    readonly="readonly"
                    style="width:70px"
                    />
</div>
EOH;
$_d2389373c4d226ef389a44a6ea431427c26dd7a9[]
=
array(
'name'=>'expire_date',
'label'=>$this->__('Date to Expire'),
'type'=>'html',
'html'=>$_e60edee9bbcec9af2084c09a7e3e3bf5a0643e9f,
);
$_d2389373c4d226ef389a44a6ea431427c26dd7a9[]
=
array(
'name'=>'active',
'label'=>$this->__('Active'),
'required'=>false,
'type'=>'checkbox',
'style'=>'',
'cssclass'=>'',
);

foreach
($_d2389373c4d226ef389a44a6ea431427c26dd7a9
as
$_dc7f46f519b41520210eda842ff23db60112e8e7=>$_54323be853e934686a9fca46e2f91c8ead26624e)
{
if
(!isset($_54323be853e934686a9fca46e2f91c8ead26624e['onlyNewRequired']))
$_54323be853e934686a9fca46e2f91c8ead26624e['onlyNewRequired']
=
false;
if
(!isset($_54323be853e934686a9fca46e2f91c8ead26624e['onlyNewValue']))
$_54323be853e934686a9fca46e2f91c8ead26624e['onlyNewValue']
=
false;
if
(!isset($_54323be853e934686a9fca46e2f91c8ead26624e['readonly']))
$_54323be853e934686a9fca46e2f91c8ead26624e['readonly']
=
false;
$_d2389373c4d226ef389a44a6ea431427c26dd7a9[$_dc7f46f519b41520210eda842ff23db60112e8e7]
=
$_54323be853e934686a9fca46e2f91c8ead26624e;
}
$_fb05a43927cff438da7fdbd15552ff3227bd4b96->getElement('sublogins')->setRenderer(
Mage::getSingleton('core/layout')->createBlock('sublogin/tableinput')
->addAfterJs('mageb2b/sublogin/form.js')
->setDisplay(
array(
'idfield'=>'id',
'addbutton'=>$this->__('Add'),
'fields'=>$_d2389373c4d226ef389a44a6ea431427c26dd7a9,
))
);
$_205d95c001c842f933e3b55fa4e902d5d2fdd0af
=
Mage::getModel('sublogin/sublogin')->getCollection()
->addFieldToFilter('entity_id',
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getId())
->addOrder('id',
'ASC');
$_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->setSublogins($_205d95c001c842f933e3b55fa4e902d5d2fdd0af->getItems());
$_fb05a43927cff438da7fdbd15552ff3227bd4b96->setValues($_b803c43bc8b3ef64b7d19b78f670cdbd11dacf8c->getData());
$this->setForm($_fb05a43927cff438da7fdbd15552ff3227bd4b96);
return
$this;
}
}