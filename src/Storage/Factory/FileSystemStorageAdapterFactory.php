<?php

/**
 * 
 * @author Bishwo Prasad Lamichhane <bishwo.prasad@gmail.com>
 */

namespace Zf3FileUpload\Storage\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

use Zf3FileUpload\Storage\FileSystemStorageAdapter;

class FileSystemStorageAdapterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null)
    {
        $moduleOptions     = $container->get(\Zf3FileUpload\ModuleOptions\ModuleOptions::class);
        
        $ret = false;
        $fileCalssName = '\\'.$moduleOptions->getEntity();
        $fileObject = new $fileCalssName();

        $ret = new FileSystemStorageAdapter($fileObject);
        
        if($ret==false){
            throw new \Exception("Cannot create storage adapter! Check your configuration.");
        }
        
        return $ret;
    }
}
