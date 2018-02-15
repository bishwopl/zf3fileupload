<?php

/**
 * 
 * @author Bishwo Prasad Lamichhane <bishwo.prasad@gmail.com>
 */

namespace Zf3FileUpload\Storage;

interface StorageInterface {

    /**
     * Path of file
     * @param string $filePath
     * @param array $attributes
     */
    public function store($filePath, $attributes);

    /**
     * 
     * @param type $fileUuid
     * @param type $filePath
     */
    public function remove($fileUuid, $filePath);

    /**
     * @param type $pathOrId
     * @return \Zf3FileUpload\Entity\FileEntityInterface 
     */
    public function createFileObjectFromUploadNameandFileId($uploadName, $fileId);
    
     /**
     * @param type $pathOrId
     * @return \Zf3FileUpload\Entity\FileEntityInterface  | NULL
     */
    public function fetchObjectFromUploadNameandFileId($uploadName, $fileId);
    
    public function fetchAllFromUploadName($uploadName);

    public function createFileObjectFromPath($path);

}
