<?php

/**
 * 
 * @author Bishwo Prasad Lamichhane <bishwo.prasad@gmail.com>
 */

namespace Zf3FileUpload\Storage;

use Zf3FileUpload\Entity\FileEntityInterface;

class FileSystemStorageAdapter implements StorageInterface{
    
    protected $fileObject;

    public function __construct(FileEntityInterface $fileObject) {
        $this->fileObject = $fileObject;
    }

    public function remove($path) {
        if(is_file($path)){
            unlink($path);
        }
        return true;
    }

    public function store($filePath, $attributes) {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $dir = pathinfo($filePath, PATHINFO_DIRNAME);
        if(isset($attributes['randomizeName'])&&$attributes['randomizeName']==TRUE){
            $newname = $dir.'/'.bin2hex(openssl_random_pseudo_bytes(10)).'.'.$ext;
        }
        elseif(isset($attributes['newName'])&&trim($attributes['newName'])!=''){
            $newname = $dir.'/'.trim($attributes['newName']).'.'.$ext;
        }
        else{
            $newname = preg_replace('/\s+/', '', $filePath);
        }
        rename($filePath, $newname);
        
        return $this->createFileObjectFromPathorId($newname);
    }

    public function createFileObjectFromPathorId($pathOrId): FileEntityInterface {
        $fileObj = clone $this->fileObject;
        
        if($fileObj instanceof FileEntityInterface && is_file($pathOrId)){
            
            $uuid = \Ramsey\Uuid\Uuid::uuid4();
            $ext     = pathinfo($pathOrId, PATHINFO_EXTENSION);
            $name    = $uuid.'.'.$ext;
            $size    = filesize($pathOrId);
            $finfo   = finfo_open(FILEINFO_MIME_TYPE);
            $mime    = finfo_file($finfo, $pathOrId);
            $content = file_get_contents($pathOrId);
            
            $fileObj->setFileId($uuid);
            $fileObj->setContent($content);
            $fileObj->getExtention($ext);
            $fileObj->setMime($mime);
            $fileObj->setName($name);
            $fileObj->setSize($size);

        }
        return $fileObj;
    }

    public function fetchObjectFromPathorId($pathOrId): FileEntityInterface {
        return $this->createFileObjectFromPathorId($pathOrId);
    }

}