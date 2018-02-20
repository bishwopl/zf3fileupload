<?php

/**
 * 
 * @author Bishwo Prasad Lamichhane <bishwo.prasad@gmail.com>
 */

namespace Zf3FileUpload\Storage;

use Doctrine\Common\Persistence\ObjectManager;
use Zf3FileUpload\Entity\FileEntityInterface;
use Zf3FileUpload\Storage\StorageInterface;
use Zend\Session\Container;

class DoctrineStorageAdapter implements StorageInterface {

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager 
     */
    protected $objectManager;

    /**
     * @var FileUpload\Entity\FileEntityInterface 
     */
    protected $fileObject;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $repository;

    public function __construct(ObjectManager $objectManager, FileEntityInterface $fileObject) {
        $this->objectManager = $objectManager;
        $this->fileObject = $fileObject;
        $this->repository = $this->objectManager->getRepository(get_class($fileObject));
    }

    public function remove($fileUuid, $filePath) {
        $obj = $this->fetchObjectFromUploadNameandFileId('',$fileUuid);
        $this->objectManager->remove($obj);
        $this->objectManager->flush();
    }

    public function store($file, $attributes) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $dir = pathinfo($file, PATHINFO_DIRNAME);
        
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
        
        rename($file, $newname);
        
        $fileObj = $this->createFileObjectFromPath($newname);
        if($fileObj instanceof \Zf3FileUpload\Entity\FileEntityInterface){
            $this->objectManager->persist($fileObj);
            $this->objectManager->flush();
            $fileObj = $this->repository->findOneBy([ 'name' => $fileObj->getName() ]);
        }
        return $fileObj;
    }

    public function createFileObjectFromUploadNameandFileId($uploadName, $fileId) {

        $fileObj = $this->fetchObjectFromPathorId($pathOrId);
        if ($fileObj instanceof \Zf3FileUpload\Entity\FileEntityInterface) {
            return $fileObj;
        }

        if (is_file($pathOrId)) {
            $fileObj = clone $this->fileObject;
            $ext = strtolower(pathinfo($pathOrId, PATHINFO_EXTENSION));
            $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $pathOrId);
            $content = file_get_contents($pathOrId);

            $fileObj->setContent($content);
            $fileObj->setExtention($ext);
            $fileObj->setMime($mime);
            $fileObj->setName($fileObj->getId() . '.' . $fileObj->getExtention());
            $fileObj->setSize(filesize($pathOrId));
            $this->objectManager->persist($fileObj);
            $this->objectManager->flush();
            unlink($pathOrId);
        }
        return $fileObj;
    }

    public function fetchObjectFromUploadNameandFileId($uploadName, $fileId) {
        $obj = $this->repository->findOneBy(['fileId' => $fileId]);
        return $obj;
    }

    public function createFileObjectFromPath($path) {
        $fileObj = NULL;
        if (is_file($path)) {
            $fileObj = clone $this->fileObject;
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
            $content = file_get_contents($path);

            $fileObj->setContent($content);
            $fileObj->setExtention($ext);
            $fileObj->setMime($mime);
            $fileObj->setName($path);
            $fileObj->setSize(filesize($path));
            unlink($path);
        }
        return $fileObj;
    }

    public function fetchAllFromUploadName($uploadName) {
        $ret = [];
        $sessionSuccess = new Container('FormUploadSuccessContainer');
        if($sessionSuccess->offsetExists($uploadName)){
            $ids = $sessionSuccess->$uploadName;
            foreach($ids as $fileName=>$fileUuid){
                $obj = $this->fetchObjectFromUploadNameandFileId($uploadName, $fileUuid);
                if($obj instanceof \Zf3FileUpload\Entity\FileEntityInterface){
                    $ret[] = $obj;
                }
            }
        }
        return $ret;
    }

}
