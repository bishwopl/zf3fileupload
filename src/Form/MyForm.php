<?php

/**
 * 
 * @author Bishwo Prasad Lamichhane <bishwo.prasad@gmail.com>
 */
namespace Zf3FileUpload\Form;

use Laminas\Form\Form;

class MyForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct($name);
    }
    public function init()
    {
        $this->add([
            'type' => 'fileupload',
            'name' => 'start_date',
            'attributes' => [
                'formUniqueId'      => 'photo_',
                'id'                => 'photoPathId',
                'storage'           => 'filesystem', // 'filesystem' or 'db
                'showProgress'      => TRUE,
                'multiple'          => FALSE,
                'enableRemove'      => TRUE,
                'uploadDir'         => 'data/UserData/',
                'icon'              => 'fas fa-upload',
                'successIcon'       => 'fas fa-edit',
                'errorIcon'         => 'fas fa-remove',
                'class'             => 'btn btn-default',
                'uploadText'        => 'Upload Photo',
                'successText'       => 'Change Photo',
                'errorText'         => 'Try Again',
                'uploadingText'     => 'Uploading Photo...',
                'replacePrevious'   => TRUE,
                'randomizeName'     => TRUE,
                'showPreview'       => TRUE,
                'validator' => [ 
                    'allowedExtentions' => 'jpg,png',
                    'allowedMime'       => 'image/jpeg,image/png',
                    'minSize'           => 10,
                    'maxSize'           => 500*1024,
                    'image' => [
                        'minWidth'  => 0,
                        'minHeight' => 0,
                        'maxWidth'  => 1200,
                        'maxHeight' => 1000,
                    ],
                ],
                'crop' => [
                    'width'  => 200,
                    'height' => 200,
                ],
                'preview'=>[
                    'width'  => 100,
                    'height' => 100,
                ],
                'callback'=>[
                    //first callback must be as follows others can be configured as user desires
                    //[
                    //    'object'    => 'object',
                    //    'function'  => 'name of function to call',
                    //    'parameter' => 'name(s) with full path of file(s) uploaded eparated with comma '
                    //]
                ]
            ],
            'options' => [
                'label' => 'Abc',
            ],
        ]);
        
        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Submit',
                'id' => 'submitButton',
                'class' => 'btn btn-primary'
            ],
        ]);
    }
}