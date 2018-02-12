<?php

/**
 * 
 * @author Bishwo Prasad Lamichhane <bishwo.prasad@gmail.com>
 */
namespace Zf3FileUpload\Entity;

interface FileEntityInterface {
    
    public function setFileId(\Ramsey\Uuid\UuidInterface $uuid);
    
    public function getFileId();

    /**
     * Set name
     *
     * @param string $name
     *
     * @return File
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set extention
     *
     * @param string $extention
     *
     * @return File
     */
    public function setExtention($extention);

    /**
     * Get extention
     *
     * @return string
     */
    public function getExtention();

    /**
     * Set mime
     *
     * @param string $mime
     *
     * @return File
     */
    public function setMime($mime);

    /**
     * Get mime
     *
     * @return string
     */
    public function getMime();

    /**
     * Set size
     *
     * @param integer $size
     *
     * @return File
     */
    public function setSize($size);

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize();

    /**
     * Set content
     *
     * @param string $content
     *
     * @return File
     */
    public function setContent($content);

    /**
     * Get content
     *
     * @return string
     */
    public function getContent();
}