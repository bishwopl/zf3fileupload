<?php
namespace Zf3FileUpload;
return [
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => __DIR__ . '/../src/'.'/Entity',
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ],
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'fileUpload' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/fileupload',
                    'defaults' => [
                        'controller' => \Zf3FileUpload\Controller\FileUploadController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'test' => [
                        'type' => 'Segment' ,
                        'options' => [
                            'route' => '/test',
                            'defaults' => [
                                'controller' => \Zf3FileUpload\Controller\TestFileUploadController::class ,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'upload' => [
                        'type' => 'Segment' ,
                        'options' => [
                            'route' => '/upload',
                            'defaults' => [
                                'controller' => \Zf3FileUpload\Controller\UploadController::class ,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'getUpload' => [
                        'type' => 'Segment' ,
                        'options' => [
                            'route' => '/get-uploaded-file[/:uploadname][/:filename]',
                            'defaults' => [
                                'controller' => \Zf3FileUpload\Controller\DownloadController::class,
                                'action'     => 'getUpload',
                            ],
                        ],
                    ],
                    'preview' => [
                        'type' => 'Segment' ,
                        'options' => [
                            'route' => '/preview[/:uploadname][/:filename]',
                            'defaults' => [
                                'controller' => \Zf3FileUpload\Controller\FileUploadController::class,
                                'action'     => 'preview',
                            ],
                        ],
                    ],
                    'removeUpload' => [
                        'type' => 'Segment' ,
                        'options' => [
                            'route' => '/remove-uploaded-file[/:uploadname][/:filename]',
                            'defaults' => [
                                'controller' => \Zf3FileUpload\Controller\DeleteController::class,
                                'action'     => 'removeUpload',
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