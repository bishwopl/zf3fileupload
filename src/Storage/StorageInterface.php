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
     * @param string $path
     */
    public function remove($path);

    /**
     * @param type $pathOrId
     * @return \Zf3FileUpload\Entity\FileEntityInterface 
     */
    public function createFileObjectFromPathorId($pathOrId);
    
     /**
     * @param type $pathOrId
     * @return \Zf3FileUpload\Entity\FileEntityInterface  | NULL
     */
    public function fetchObjectFromPathorId($pathOrId);
}
