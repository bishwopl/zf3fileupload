<?php
namespace Zf3FileUpload\Form\View\Helper\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

use Zf3FileUpload\Form\View\Helper\FormFileUpload;

class FormFileUploadFactory implements FactoryInterface
{   
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        $uploadService = $container->get(\Zf3FileUpload\Service\FileUploadService::class);
        return new FormFileUpload($uploadService);
    }
}