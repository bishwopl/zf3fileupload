<?php
namespace Zf3FileUpload\Controller\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class FileUploadControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        $uploadService = $container->get(\Zf3FileUpload\Service\FileUploadService::class);
        $requestedNameAbs = '\\'.$requestedName;
        return new $requestedNameAbs($uploadService);
    }
}
