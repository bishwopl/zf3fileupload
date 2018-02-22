<?php
namespace Zf3FileUpload\Service\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

use Zf3FileUpload\Service\FileUploadService;

class FileUploadServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        $moduleOptions  = $container->get(\Zf3FileUpload\ModuleOptions\ModuleOptions::class);
        $adapters       = [
            'filesystem' => $container->get(\Zf3FileUpload\Storage\FileSystemStorageAdapter::class),
            'db'         => $container->get(\Zf3FileUpload\Storage\DoctrineStorageAdapter::class),
        ];
        return new FileUploadService($adapters, $container, $moduleOptions);
    }
}

