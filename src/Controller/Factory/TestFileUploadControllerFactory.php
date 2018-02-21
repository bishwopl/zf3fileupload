<?php
namespace Zf3FileUpload\Controller\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

use Zf3FileUpload\Form\MyForm;

class TestFileUploadControllerFactory implements FactoryInterface
{   
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        $em                = $container->get('doctrine.entitymanager.orm_default');
        $moduleOptions     = $container->get(\Zf3FileUpload\ModuleOptions\ModuleOptions::class);
        $fileUploadService = $container->get(\Zf3FileUpload\Service\FileUploadService::class);
        $translator        = $container->get('translator');
        $formManager       = $container->get('FormElementManager');
        $myform            = $formManager->get(MyForm::class);
        $requestedNameAbs = '\\'.$requestedName;
        return new $requestedNameAbs(
            $moduleOptions, 
            $fileUploadService, 
            $em, 
            $myform, 
            $translator
        );
    }
}

