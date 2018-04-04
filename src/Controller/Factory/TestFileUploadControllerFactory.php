<?php
namespace Zf3FileUpload\Controller\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

use Zf3FileUpload\Form\MyForm;

class TestFileUploadControllerFactory implements FactoryInterface
{   
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        $formManager       = $container->get('FormElementManager');
        $myform            = $formManager->get(MyForm::class);
        $requestedNameAbs = '\\'.$requestedName;
        return new $requestedNameAbs($myform);
    }
}

