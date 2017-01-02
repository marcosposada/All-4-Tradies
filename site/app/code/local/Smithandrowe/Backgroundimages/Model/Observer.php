<?php

class Smithandrowe_Backgroundimages_Model_Observer
{

	/**
	 * Add image field to form
	 *
	 * @param Varien_Event_Observer $observer
	 *
	 * @return void
	 */
	public function prepareForm(Varien_Event_Observer $observer)
	{

		$form = $observer->getEvent()->getForm();
		$form->setEnctype('multipart/form-data');
		$fieldset = $form->addFieldset('image_fieldset',
			array(
				'legend' => 'Image',
				'class' => 'fieldset-wide'
			)
		);

		$fieldset->addField('background', 'image', array(
				'name' => 'background',
                'type' => 'multiple',
				'label' => 'Background image',
				'title' => 'Background image'
			));

        $fieldset->addField('promo', 'image', array(
            'name' => 'promo',
            'type' => 'multiple',
            'label' => 'Promo image',
            'title' => 'Promo image'
        ));
	}

	/**
	 * Save background image
	 *
	 * @param Varien_Event_Observer $observer
	 *
	 * @return void
	 */
	public function savePage(Varien_Event_Observer $observer)
	{
		$model = $observer->getEvent()->getPage();
		$request = $observer->getEvent()->getRequest();

		// DEBUG VARIABLES

		$post = $request->getPost();
		$filesvar = $_FILES;
        $store = Mage::getModel('core/store')->load($post['stores'][0]);
        $storeCode = $store->getCode();
        $vars = array('background','promo');

        foreach ($vars as $x) {
            $file_name = $filesvar[$x]['name'];
            if ($file_name) {
                $uploader = new Varien_File_Uploader($x);
                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);

                //Set the media path
                $relpath = 'wysiwyg'.DS.$storeCode.DS.'blocks'.DS.'cms'.DS.$x.DS;
                $media_path  = Mage::getBaseDir('media') .DS.$relpath;
                //$relpath =  'background' . DS . $storeCode . DS;

                // Set thumbnail name
               // $ext = $x.'-'.$storeCode.'-';

                //$file_name = $ext.$name;

                // Upload the image

                $uploader->save($media_path, $file_name);

                $data[$x] = $relpath . $file_name;

                // Set thumbnail name
                $model->setData($x,$data[$x]);

                /*
                if ($x == 'background') {
                    $model->setBackground($x);
                } elseif ($x == 'promo') {
                    $model->setPromo($x);
                }*/

            } else {
                //Mage::log('no bg');
                $data = $request->getPost();
                if (isset($data[$x]['delete'])) {
                    if($data[$x]['delete'] == 1) {
                        $data[$x] = '';
                        $model->setData($x,implode($request->getPost($x)));
                    } else {
                        unset($data[$x]);
                        $model->setData($x,implode($request->getPost($x)));
                    }
                } else {
                    if(isset($data[$x])) {
                        unset($data[$x]);
                        $model->setData($x,implode($request->getPost($x)));
                    }
                }

            }

           // Mage::log($x.' - '.);
        }
        /*
		if ($filesvar['background']['name'] && $filesvar['background']["name"] != '') {

			$uploader = new Varien_File_Uploader('background');
			$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
			$uploader->setAllowRenameFiles(false);
			$uploader->setFilesDispersion(false);

			// Set media as the upload dir
                $ext = 'bg-'.$storeCode.'-';
				$media_path  = Mage::getBaseDir('media') . DS . 'background' . DS . $storeCode . DS;
				$relpath =  'background' . DS . $storeCode . DS;

			// Set thumbnail name
			$file_name = $ext.$_FILES['background']['name'];

			// Upload the image

			$uploader->save($media_path, $file_name);

			$data['background'] = $relpath . $file_name;

			// Set thumbnail name
			$data['background'] = $data['background'];
			$model->setBackground($data['background']);

		}
        */
    }



	/**
	 * Shortcut to getRequest
	 *
	 * @return Mage_Core_Controller_Request_Http
	 */
	protected function _getRequest()
	{
		return Mage::app()->getRequest();
	}
}