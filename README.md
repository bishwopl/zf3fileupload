(Not ready for production)

# zf3fileupload
Custom file upload element for zf3, supports filesystem and database storage(using DoctrineORM)

# Requirements
* [php: ^5.6 || ^7.0](http://php.net/)
* [zendframework/zend-cache: ^2.7.1](https://github.com/zendframework/zend-cache)
* [zendframework/zend-mvc-form: ^1.0](https://github.com/zendframework/zend-mvc-form)
* [zendframework/zend-mvc-plugins: ^1.0.1](https://github.com/zendframework/zend-mvc-plugins)
* [zendframework/zend-session: ^2.7.1](https://github.com/zendframework/zend-session)
* [zendframework/zend-servicemanager-di: ^1.0](https://github.com/zendframework/zend-servicemanager-di)
* [zendframework/zend-file: ^2.7](https://github.com/zendframework/zend-file)
* [doctrine/doctrine-orm-module: ^1.1](https://github.com/doctrine/DoctrineORMModule)
* [ramsey/uuid: ^3.7](https://github.com/ramsey/uuid)
* [ramsey/uuid-doctrine: ^1.4](https://github.com/ramsey/uuid-doctrine)
* [masterexploder/phpthumb: ^2.1](https://github.com/masterexploder/phpthumb)
* [jquery form plugin](http://malsup.com/jquery/form/)
* [Bootstrap 3.* ](https://getbootstrap.com/docs/3.3/)

# Sample Element
```php
    public function init()
    {
        $this->add([
            'type' => 'fileupload',
            'name' => 'start_date',
            'attributes' => [
                'formUniqueId'      => 'photo_',
                'id'                => 'photoPathId',
                'storage'           => 'db', // 'filesystem' or 'db
                'showProgress'      => TRUE,
                'multiple'          => TRUE,
                'enableRemove'      => TRUE,
                'uploadDir'         => 'data/UserData/',
                'icon'              => 'fa fa-upload',
                'successIcon'       => 'fa fa-pencil',
                'errorIcon'         => 'fa fa-remove',
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
    }
```
