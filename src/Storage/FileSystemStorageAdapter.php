<?php

/**
 * 
 * @author Bishwo Prasad Lamichhane <bishwo.prasad@gmail.com>
 */

namespace Zf3FileUpload\Storage;

use Zf3FileUpload\Entity\FileEntityInterface;
use Zend\Session\Container;

class FileSystemStorageAdapter implements StorageInterface{
    
    protected $fileObject;

    public function __construct(FileEntityInterface $fileObject) {
        $this->fileObject = $fileObject;
    }

    public function remove($fileUuid, $filePath) {
        if(is_file($filePath)){
            unlink($filePath);
        }
    }

    public function store($filePath, $attributes) {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $dir = pathinfo($filePath, PATHINFO_DIRNAME);
        
        if(isset($attributes['randomizeName'])&&$attributes['randomizeName']==TRUE){
            $uuid = \Ramsey\Uuid\Uuid::uuid4();
            $newname = $dir.'/'.$uuid.'.'.$ext;
        }
        elseif(isset($attributes['newName'])&&trim($attributes['newName'])!=''){
            $newname = $dir.'/'.trim($attributes['newName']).'.'.$ext;
        }
        else{
            $newname = preg_replace('/\s+/', '', $filePath);
        }
        
        rename($filePath, $newname);
        $fileObj = $this->createFileObjectFromPath($newname);
        
        return $fileObj;
    }

    public function createFileObjectFromUploadNameandFileId($uploadName, $fileId) {
        $fileObj = NULL;
        
        $sessionSuccess = new Container('FormUploadSuccessContainer');
        $previousUploads = $sessionSuccess->$uploadName;
        
        foreach($previousUploads as $fileName=>$fileIdLoop){
            $fileIdLoop = is_object($fileIdLoop)?$fileIdLoop->toSring():$fileIdLoop;
            if($fileId==$fileIdLoop){
                echo 'dkjskdfsd';
            }
        }
        
        if($fileObj instanceof FileEntityInterface && is_file($fileName)){
            $fileObj = clone $this->fileObject;
            $uuid = \Ramsey\Uuid\Uuid::uuid4();
            
            $ext     = pathinfo($pathOrId, PATHINFO_EXTENSION);
            $name    = $pathOrId;
            $size    = filesize($pathOrId);
            $finfo   = finfo_open(FILEINFO_MIME_TYPE);
            $mime    = finfo_file($finfo, $pathOrId);
            $content = file_get_contents($pathOrId);
            
            $fileObj->setId($uuid);
            $fileObj->setContent($content);
            $fileObj->getExtention($ext);
            $fileObj->setMime($mime);
            $fileObj->setName($name);
            $fileObj->setSize($size);
        }
        return $fileObj;
    }

    public function fetchObjectFromUploadNameandFileId($uploadName, $fileId){
        $fileObject = NULL;
        $sessionSuccess = new Container('FormUploadSuccessContainer');
        if($sessionSuccess->offsetExists($uploadName)){
            $files = $sessionSuccess->$uploadName;
        }
        foreach($files as $fileName=>$fileUuid){
            $fileUuidString = $fileUuid->toString();
            if($fileUuidString == $fileId){
                $fileObject = $this->createFileObjectFromPath($fileName);
                $fileObject->setId($fileUuid);
                break;
            }
        }
        return $fileObject;
    }
    
    public function createFileObjectFromPath($path) {
        $fileObj = NULL;
        if (is_file($path)) {
            $uuid = \Ramsey\Uuid\Uuid::uuid4();
            $fileObj = clone $this->fileObject;
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
            $content = file_get_contents($path);

            $fileObj->setId($uuid);
            $fileObj->setContent($content);
            $fileObj->setExtention($ext);
            $fileObj->setMime($mime);
            $fileObj->setName($path);
            $fileObj->setSize(filesize($path));
        }
        return $fileObj;
    }

    public function fetchAllFromUploadName($uploadName) {
        $ret = [];
        $sessionSuccess = new Container('FormUploadSuccessContainer');
        if($sessionSuccess->offsetExists($uploadName)){
            $ids = $sessionSuccess->$uploadName;
            foreach($ids as $fileName=>$fileUuid){
                $obj = $this->createFileObjectFromPath($fileName);
                if($obj instanceof \Zf3FileUpload\Entity\FileEntityInterface){
                    $obj->setId($fileUuid);
                    $ret[] = $obj;
                }
            }
        }
        return $ret;
    }

}