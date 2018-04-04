<?php

namespace Zf3FileUpload;

use Ramsey\Uuid\Doctrine\UuidType;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => __DIR__ . '/../src/' . '/Entity',
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ],
            ],
        ],
        'configuration' => [
            'orm_default' => [
                'types' => [
                    UuidType::NAME => UuidType::class,
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            \Zf3FileUpload\Controller\TestFileUploadController::class =>
            \Zf3FileUpload\Controller\Factory\TestFileUploadControllerFactory::class,
            \Zf3FileUpload\Controller\FileUploadController::class =>
            \Zf3FileUpload\Controller\Factory\FileUploadControllerFactory::class,
            \Zf3FileUpload\Controller\DownloadController::class =>
            \Zf3FileUpload\Controller\Factory\FileUploadControllerFactory::class,
            \Zf3FileUpload\Controller\UploadController::class =>
            \Zf3FileUpload\Controller\Factory\FileUploadControllerFactory::class,
            \Zf3FileUpload\Controller\DeleteController::class =>
            \Zf3FileUpload\Controller\Factory\FileUploadControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            \Zf3FileUpload\ModuleOptions\ModuleOptions::class =>
            \Zf3FileUpload\ModuleOptions\Factory\ModuleOptionsFactory::class,
            \Zf3FileUpload\Service\FileUploadService::class =>
            \Zf3FileUpload\Service\Factory\FileUploadServiceFactory::class,
            \Zf3FileUpload\Storage\StorageInterface::class =>
            \Zf3FileUpload\Storage\Factory\StorageAdapterFactory::class,
            \Zf3FileUpload\Storage\FileSystemStorageAdapter::class =>
            \Zf3FileUpload\Storage\Factory\FileSystemStorageAdapterFactory::class,
            \Zf3FileUpload\Storage\DoctrineStorageAdapter::class =>
            \Zf3FileUpload\Storage\Factory\DoctrineStorageAdapterFactory::class,
        ],
    ],
    'form_elements' => [
        'aliases' => [
            'fileupload' => \Zf3FileUpload\Form\Element\FileUpload::class,
        ],
        'factories' => [
            \Zf3FileUpload\Form\Element\FileUpload::class => InvokableFactory::class,
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'FormElement' => \Zf3FileUpload\Form\View\Helper\FormElement::class,
            'formElement' => \Zf3FileUpload\Form\View\Helper\FormElement::class,
            'formelement' => \Zf3FileUpload\Form\View\Helper\FormElement::class,
        ],
        'factories' => [
            'formFileUpload' => \Zf3FileUpload\Form\View\Helper\Factory\FormFileUploadFactory::class
        ],
    ],
    'router' => [
        'routes' => [
            'fileUpload' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/fileupload',
                    'defaults' => [
                        'controller' => \Zf3FileUpload\Controller\FileUploadController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'test' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/test',
                            'defaults' => [
                                'controller' => \Zf3FileUpload\Controller\TestFileUploadController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'upload' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/upload',
                            'defaults' => [
                                'controller' => \Zf3FileUpload\Controller\UploadController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'getUpload' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/get-uploaded-file[/:uploadname][/:filename]',
                            'defaults' => [
                                'controller' => \Zf3FileUpload\Controller\DownloadController::class,
                                'action' => 'getUpload',
                            ],
                        ],
                    ],
                    'preview' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/preview[/:uploadname][/:filename]',
                            'defaults' => [
                                'controller' => \Zf3FileUpload\Controller\FileUploadController::class,
                                'action' => 'preview',
                            ],
                        ],
                    ],
                    'removeUpload' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/remove-uploaded-file[/:uploadname][/:filename]',
                            'defaults' => [
                                'controller' => \Zf3FileUpload\Controller\DeleteController::class,
                                'action' => 'removeUpload',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'circlical' => [
        'user' => [
            'guards' => [
                'fileupload' => [
                    'controllers' => [
                        Controller\FileUploadController::class => [
                            'default' => [],
                            'actions' => [
                                'index' => [],
                            ],
                        ],
                        Controller\TestFileUploadController::class => [
                            'default' => [],
                            'actions' => [
                                'index' => [],
                            ],
                        ],
                        Controller\DeleteController::class => [
                            'default' => [],
                            'actions' => [
                                'index' => [],
                            ],
                        ],
                        Controller\DownloadController::class => [
                            'default' => [],
                            'actions' => [
                                'index' => [],
                            ],
                        ],
                        Controller\UploadController::class => [
                            'default' => [],
                            'actions' => [
                                'index' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'file-upload' => __DIR__ . '/../view',
        ],
        'template_map' => [
            'file-upload/delete' => __DIR__ . '/../view/zf3-file-upload/upload/index.phtml',
        ],
    ],
];
