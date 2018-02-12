<?php
namespace Zf3FileUpload\Service;

use Zend\Session\Container;
use Zend\InputFilter;
use Zf3FileUpload\Form\BaseUploadForm;
use Zf3FileUpload\Storage\StorageInterface;
use Zf3FileUpload\ModuleOptions\ModuleOptions;

class FileUploadService{
    
    /**
     * @var \Zf3FileUpload\ModuleOptions\ModuleOptions
     */
    protected $moduleOptions;
    
    /**
     * @var \Zf3FileUpload\Storage\StorageInterface 
     */
    protected $storageAdapter;
    
    protected $serviceLocator;

    public function __construct(
            StorageInterface $storageAdapter, 
            $serviceLocator, 
            ModuleOptions $moduleOptions
    ) {
        $this->storageAdapter = $storageAdapter;
        $this->serviceLocator = $serviceLocator;
        $this->moduleOptions  = $moduleOptions;
    }
    
    public function getForm()
    {
        return new BaseUploadForm();
    }
    
    /**
     * 
     * @return \Zf3FileUpload\Entity\FileEntityInterface
     */
    public function getFileObject(){
        $fileEntityname = '\\'.$this->moduleOptions->getEntity();
        return new $fileEntityname();
    }
    
    public function getModuleOptions(){
        return $this->moduleOptions;
    }

    public function getInutFilter($validators, $uploadName){
        $inputFilter = new InputFilter\InputFilter();

        $fileInput = new InputFilter\FileInput($uploadName);
        $fileInput->setRequired(true);
        
        $minSize = $validators['minSize']==''?false:$validators['minSize'];
        $maxSize = $validators['maxSize']==''?false:$validators['maxSize'];
        $allowedMime = $validators['allowedMime']==''?false:$validators['allowedMime'];
        $allowedExtentions = $validators['allowedExtentions']==''?false:$validators['allowedExtentions'];
        if(!array($allowedExtentions)){
            $allowedExtentions = explode(',', $allowedExtentions);
        }
        if($minSize!==false){
            $fileInput->getValidatorChain()->attachByName('filesize', array('min' => $minSize));
        }
        
        if($maxSize!==false){
            $fileInput->getValidatorChain()->attachByName('filesize', array('max' => $maxSize));
        }
        
        if($allowedMime!==false){
            $fileInput->getValidatorChain()->attachByName('filemimetype', array('mimeType' => $allowedMime));
        }
        
        if($allowedExtentions!==false){
            $extensionvalidator = new \Zend\Validator\File\Extension(array('extension'=>$allowedExtentions));
            $fileInput->getValidatorChain()->attach($extensionvalidator);
        }
        
        if(isset($validators['image'])){
            $imgValidator = $validators['image'];
            
            $minWidth = isset($imgValidator['minWidth'])?$imgValidator['minWidth']:false;
            $minHeight = isset($imgValidator['minHeight'])?$imgValidator['minHeight']:false;
            
            $maxWidth = isset($imgValidator['maxWidth'])?$imgValidator['maxWidth']:false;
            $maxHeight = isset($imgValidator['maxHeight'])?$imgValidator['maxHeight']:false;
            
            
            if($minWidth!==false && $minHeight!==false){
                $fileInput->getValidatorChain()->attachByName('fileimagesize', 
                array('minWidth' => $minWidth, 'minHeight' => $minHeight));
            }
            
            if($minWidth!==false && $minHeight!==false){
                $fileInput->getValidatorChain()->attachByName('fileimagesize', 
                array('maxWidth' => $maxWidth, 'maxHeight' => $maxHeight));
            }
        }
        
        $inputFilter->add($fileInput);
        return $inputFilter;
    } 
    
    public function cropImage($cropDim, $files){
        if(!is_array($cropDim) || !is_array($files)){
            return;
        }
        
        $width = isset($cropDim['width'])?$cropDim['width']:1;
        $height = isset($cropDim['height'])?$cropDim['height']:NULL;
        
        foreach($files as $f){
            if(!file_exists($f)){
                continue;
            }
            $thumb = new \PHPThumb\GD($f);
            if($height==NULL || $height==0){
                $thumb->adaptiveResize($width, NULL);
            }
            else{
                $thumb->adaptiveResize($width, $height);
            }
            $thumb->save($f);
        }
    }

    public function removePreviousUploads($atributes,$uploadName){
        $sessionSuccess = new Container('FormUploadSuccessContainer');
        if($atributes['replacePrevious']==true){
            $files = $sessionSuccess->offsetGet($uploadName);
            foreach ($files as $key=>$f){
                if(file_exists($f)){
                    unlink($f);
                }
                unset($files[$key]);
            }
            $sessionSuccess->$uploadName = $files;
        }
        return;
    }
    
    public function clearUploaInfo(){
        /*$sessionSuccess = new Container('FormUploadSuccessContainer');
        $session = new Container('FormUploadFormContainer');
        $session->exchangeArray([]);
        $sessionSuccess->exchangeArray([]);
        */
    }

    public function callBack($callBack, $uploadedFileNames){
        if(!is_array($callBack)){
            return;
        }
        $count = 0;
        foreach($callBack as $c){
            if(isset($c['object'])&&isset($c['function'])&&isset($c['parameter'])){
                //echo 'sdfjsdf';
                $object = $c['object'];
                $object = !is_object($object)?$object = $this->serviceLocator->get($object):$object;
                $function = $c['function'];
                $parameter = $count==0?$uploadedFileNames:$parameter = $c['parameter'];
                $count++;
                call_user_func(array($object,$function),$parameter);
            }
            else{
                //echo 'Not Properly Configured';
            }
        }
    }
    
    /**
     * 
     * @param type $filePath
     * @param type $attributes
     * @return \Zf3FileUpload\Entity\FileEntityInterface
     */
    public function storeFile($filePath, $attributes){
        return $this->storageAdapter->store($filePath, $attributes);
    }
    
    public function getFileObjectListFromUploadName($uploadName){
        $ret = [];
        $sessionSuccess = new Container('FormUploadSuccessContainer');
        if($sessionSuccess->offsetExists($uploadName)){
            $ids = $sessionSuccess->$uploadName;
            foreach($ids as $id){
                $obj = $this->storageAdapter->createFileObjectFromPathorId($id);
                if($obj instanceof \Zf3FileUpload\Entity\FileEntityInterface){
                    $ret[] = $obj;
                }
            }
        }
        return $ret;
    }

    /**
     * 
     * @param string $path
     * @return \Zf3FileUpload\Entity\FileEntityInterface
     */
    public function getFileObjectFromPathOrId($path){
        return $this->storageAdapter->createFileObjectFromPathorId($path);
    }
    
    public function removeFileFromIdOrPath($path){
        return $this->storageAdapter->remove($path);
    }
    
    public function getFileObjectFromUploadNameAndFileName($uploadName, $fileName){
        $sessionSuccess = new Container('FormUploadSuccessContainer');
        $fileObject = [];
        
        if($sessionSuccess->offsetExists($uploadName)){
            $files = $sessionSuccess->$uploadName;
        }
        
        foreach($files as $f){
            if($fileName == basename($f)){
                $fileObject = $this->storageAdapter->createFileObjectFromPathorId($f);
            }
        }
        return $fileObject;
    }
    
    public function removeFileFromUploadNameAndFileName($uploadName, $fileName){
        $sessionSuccess = new Container('FormUploadSuccessContainer');
        $session = new Container('FormUploadFormContainer');
        $atributes = $session->offsetGet($uploadName);
        
        if($atributes['enableRemove']!==TRUE){
            return;
        }
        
        if($sessionSuccess->offsetExists($uploadName)){
            $files = $sessionSuccess->$uploadName;
        }
        
        $remaining = [];
        foreach($files as $f){
            if($fileName == basename($f)){
                $this->storageAdapter->remove($f);
            }
            else{
                $remaining[] = $f;
            }
        }
        
        $sessionSuccess->$uploadName = $remaining;
    }
}