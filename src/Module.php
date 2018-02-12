<?php

namespace Zf3FileUpload;

use Zend\ServiceManager\Factory\InvokableFactory;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\Feature\FormElementProviderInterface;

class Module implements 
    AutoloaderProviderInterface, 
    ConfigProviderInterface, 
    ServiceProviderInterface, 
    ViewHelperProviderInterface,
    ControllerProviderInterface,
    FormElementProviderInterface
{
    public function getAutoloaderConfig() {
        return [
            'Zend\Loader\ClassMapAutoloader' => [
                __DIR__ . '/autoload_classmap.php',
            ],
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    public function getControllerConfig() {
        return [
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
        ];
    }

    public function getServiceConfig() {
        return [
            'factories' => [
                \Zf3FileUpload\ModuleOptions\ModuleOptions::class => 
                \Zf3FileUpload\ModuleOptions\Factory\ModuleOptionsFactory::class,
                
                \Zf3FileUpload\Service\FileUploadService::class   => 
                \Zf3FileUpload\Service\Factory\FileUploadServiceFactory::class,
                
                \Zf3FileUpload\Storage\StorageInterface::class => 
                \Zf3FileUpload\Storage\Factory\StorageAdapterFactory::class 
            ],
        ];
    }

    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getFormElementConfig()
    {
        return [
            'aliases' => [
                'fileupload' => Form\Element\FileUpload::class,
            ],
            'factories' => [
                Form\Element\FileUpload::class => InvokableFactory::class,
            ],
        ];
    }
    
    public function getViewHelperConfig() {
        return [
            'invokables' => [
                'FormElement' => \Zf3FileUpload\Form\View\Helper\FormElement::class,
                'formElement' => \Zf3FileUpload\Form\View\Helper\FormElement::class,
                'formelement' => \Zf3FileUpload\Form\View\Helper\FormElement::class,
            ],
            'factories' => [
                'formFileUpload' => \Zf3FileUpload\Form\View\Helper\Factory\FormFileUploadFactory::class
            ],
        ];
    }

}
