<?php
namespace Zf3FileUpload\ModuleOptions\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

use Zf3FileUpload\ModuleOptions\ModuleOptions;

class ModuleOptionsFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        $config = $container->get('Config');
        $optArr = isset($config['zf3_file_uploadmodule_options']) ? $config['zf3_file_uploadmodule_options'] : [];
        return new ModuleOptions($optArr);        
    }
}
