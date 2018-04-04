<?php
namespace Zf3FileUpload\ModuleOptions;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions{  
    protected $__strictMode__  = false;
    protected $testElement     = 'Hi';
    protected $objectmanager   = 'doctrine.entitymanager.orm_default';
    protected $entity          = \Zf3FileUpload\Entity\File::class;

    public function __construct($options = null) {
        parent::__construct($options);
    }

    public function getTestElement(){
        return $this->testElement;
    }
    
    public function setTestElement($testElement){
        $this->testElement = $testElement;
        return $this;
    }
    public function getObjectmanager(){
        return $this->objectmanager;
    }
    
    public function setObjectmanager($objectmanager){
        if($objectmanager!==''){
            $this->objectmanager = $objectmanager;
        }
        return $this;
    }
    
    public function getEntity(){
        return $this->entity;
    }
    
    public function setEntity($entity){
        if($entity!==''){
            $this->entity = $entity;
        }
        return $this;
    }
}

