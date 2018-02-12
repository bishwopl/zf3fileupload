<?php
namespace Zf3FileUpload\Service\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

use Zf3FileUpload\Service\FileUploadService;

class FileUploadServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        $moduleOptions     = $container->get(\Zf3FileUpload\ModuleOptions\ModuleOptions::class);
        $storageAdapter = $container->get(\Zf3FileUpload\Storage\StorageInterface::class);
        return new FileUploadService($storageAdapter, $container, $moduleOptions);
    }
}

