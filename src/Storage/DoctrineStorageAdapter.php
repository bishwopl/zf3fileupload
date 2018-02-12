<?php

/**
 * 
 * @author Bishwo Prasad Lamichhane <bishwo.prasad@gmail.com>
 */
namespace Zf3FileUpload\Storage;

use Doctrine\Common\Persistence\ObjectManager;
use Zf3FileUpload\Entity\FileEntityInterface;
use Zf3FileUpload\Storage\StorageInterface;

class DoctrineStorageAdapter implements StorageInterface{
    
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
        $this->fileObject    = $fileObject;
        $this->repository    = $this->objectManager->getRepository(get_class($fileObject));
    }

    public function remove($pathOrId) {
        $obj = $this->fetchObjectFromPathorId($pathOrId);
        $this->objectManager->remove($obj);
        $this->objectManager->flush();
    }

    public function store($file, $attributes) {
        return $this->createFileObjectFromPathorId($file);
    }

    public function createFileObjectFromPathorId($pathOrId) {
        
        $fileObj = $this->fetchObjectFromPathorId($pathOrId);
        if($fileObj instanceof \Zf3FileUpload\Entity\FileEntityInterface){
            return $fileObj;
        }
        
        if(is_file($pathOrId)){
            $fileObj = clone $this->fileObject;
            $ext     = strtolower(pathinfo($pathOrId, PATHINFO_EXTENSION));
            $mime    = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $pathOrId);
            $content = file_get_contents($pathOrId);
            
            $fileObj->setContent($content);
            $fileObj->setExtention($ext);
            $fileObj->setMime($mime);
            $fileObj->setName($fileObj->getFileId().'.'.$fileObj->getExtention());
            $fileObj->setSize(filesize($pathOrId));
            $this->objectManager->persist($fileObj);
            $this->objectManager->flush();
            unlink($pathOrId);
        }
        return $fileObj;
    }

    public function fetchObjectFromPathorId($pathOrId) {
        $obj = $this->repository->findOneBy(['name'=>$pathOrId]);
        if($obj===NULL && \Ramsey\Uuid\Uuid::isValid($pathOrId)){
            $obj = $this->repository->findOneBy(['fileId' => $pathOrId]);
        }
        return $obj;
    }

}