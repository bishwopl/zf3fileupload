<?php

namespace Zf3FileUpload\Service;

use Laminas\Session\Container;
use Laminas\InputFilter;
use Zf3FileUpload\Form\BaseUploadForm;
use Zf3FileUpload\ModuleOptions\ModuleOptions;

class FileUploadService {

    /**
     * @var \Zf3FileUpload\ModuleOptions\ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @var \Zf3FileUpload\Storage\StorageInterface 
     */
    protected $storageAdapter;

    /**
     * @var type 
     */
    protected $adapters;

    /**
     * @var type 
     */
    protected $serviceLocator;

    public function __construct(
            $adapters,
            $serviceLocator,
            ModuleOptions $moduleOptions
    ) {
        $this->adapters = $adapters;
        $this->serviceLocator = $serviceLocator;
        $this->moduleOptions = $moduleOptions;
    }

    public function getForm($uploadName) {
        return new BaseUploadForm($uploadName);
    }

    /**
     * 
     * @return \Zf3FileUpload\Entity\FileEntityInterface
     */
    public function getFileObject() {
        $fileEntityname = '\\' . $this->moduleOptions->getEntity();
        return new $fileEntityname();
    }

    public function getModuleOptions() {
        return $this->moduleOptions;
    }

    public function getInutFilter($validators, $uploadName) {
        $inputFilter = new InputFilter\InputFilter();

        $fileInput = new InputFilter\FileInput($uploadName);
        $fileInput->setRequired(true);

        $minSize = $validators['minSize'] == '' ? false : $validators['minSize'];
        $maxSize = $validators['maxSize'] == '' ? false : $validators['maxSize'];
        $allowedMime = $validators['allowedMime'] == '' ? false : $validators['allowedMime'];
        $allowedExtentions = $validators['allowedExtentions'] == '' ? false : $validators['allowedExtentions'];
        if (!array($allowedExtentions)) {
            $allowedExtentions = explode(',', $allowedExtentions);
        }
        if ($minSize !== false) {
            $fileInput->getValidatorChain()->attachByName('filesize', array('min' => $minSize));
        }

        if ($maxSize !== false) {
            $fileInput->getValidatorChain()->attachByName('filesize', array('max' => $maxSize));
        }

        if ($allowedMime !== false) {
            $fileInput->getValidatorChain()->attachByName('filemimetype', array('mimeType' => $allowedMime));
        }

        if ($allowedExtentions !== false) {
            $extensionvalidator = new \Laminas\Validator\File\Extension(array('extension' => $allowedExtentions));
            $fileInput->getValidatorChain()->attach($extensionvalidator);
        }

        if (isset($validators['image'])) {
            $imgValidator = $validators['image'];

            $minWidth = isset($imgValidator['minWidth']) ? $imgValidator['minWidth'] : false;
            $minHeight = isset($imgValidator['minHeight']) ? $imgValidator['minHeight'] : false;

            $maxWidth = isset($imgValidator['maxWidth']) ? $imgValidator['maxWidth'] : false;
            $maxHeight = isset($imgValidator['maxHeight']) ? $imgValidator['maxHeight'] : false;


            if ($minWidth !== false && $minHeight !== false) {
                $fileInput->getValidatorChain()->attachByName('fileimagesize',
                        array('minWidth' => $minWidth, 'minHeight' => $minHeight));
            }

            if ($minWidth !== false && $minHeight !== false) {
                $fileInput->getValidatorChain()->attachByName('fileimagesize',
                        array('maxWidth' => $maxWidth, 'maxHeight' => $maxHeight));
            }
        }

        $inputFilter->add($fileInput);
        return $inputFilter;
    }

    public function cropImage($cropDim, $files) {
        if (!is_array($cropDim) || !is_array($files)) {
            return;
        }

        $width = isset($cropDim['width']) ? $cropDim['width'] : 1;
        $height = isset($cropDim['height']) ? $cropDim['height'] : NULL;

        foreach ($files as $f) {
            if (!file_exists($f)) {
                continue;
            }
            $thumb = new \Zf3FileUpload\MyThumb($f);
            if ($height == NULL || $height == 0) {
                $thumb->adaptiveResize($width, NULL);
            } else {
                $thumb->adaptiveResize($width, $height);
            }
            $thumb->save($f);
        }
    }

    public function removePreviousUploads($atributes, $uploadName) {
        $this->getAppropriateAdapter($uploadName);
        $sessionSuccess = new Container('FormUploadSuccessContainer');
        if ($atributes['replacePrevious'] === TRUE || $atributes['multiple'] === FALSE) {
            $files = $sessionSuccess->offsetGet($uploadName);
            if(!is_array($files)){
                $files = [];
            }
            foreach ($files as $filePath => $fileUuid) {
                $this->storageAdapter->remove($fileUuid, $filePath);
                unset($files[$filePath]);
            }
            $sessionSuccess->$uploadName = $files;
        }
        return;
    }

    public function createDestinationDirectory($pathname) {
        if (is_dir($pathname) || empty($pathname)) {
            return true;
        }
        // Ensure a file does not already exist with the same name
        $pathname = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $pathname);
        if (is_file($pathname)) {
            trigger_error('mkdirr() File exists', E_USER_WARNING);
            return false;
        }
        // Crawl up the directory tree
        $next_pathname = substr($pathname, 0, strrpos($pathname, DIRECTORY_SEPARATOR));
        if ($this->createDestinationDirectory($next_pathname)) {
            if (!file_exists($pathname)) {
                return mkdir($pathname);
            }
        }
        return false;
    }

    public function callBack($callBack, $uploadedFileNames) {
        if (!is_array($callBack)) {
            return;
        }
        $count = 0;
        foreach ($callBack as $c) {
            if (isset($c['object']) && isset($c['function']) && isset($c['parameter'])) {
                //echo 'sdfjsdf';
                $object = $c['object'];
                $object = !is_object($object) ? $object = $this->serviceLocator->get($object) : $object;
                $function = $c['function'];
                $parameter = $count == 0 ? $uploadedFileNames : $parameter = $c['parameter'];
                $count++;
                call_user_func(array($object, $function), $parameter);
            } else {
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
    public function storeFile($filePath, $attributes, $uploadName) {
        $this->getAppropriateAdapter($uploadName);
        $fileObj = $this->storageAdapter->store($filePath, $attributes);
        if ($fileObj instanceof \Zf3FileUpload\Entity\FileEntityInterface) {
            $sessionSuccess = new Container('FormUploadSuccessContainer');
            $previousUploads = [];
            if ($sessionSuccess->offsetExists($uploadName)) {
                $previousUploads = $sessionSuccess->$uploadName;
            }
            $previousUploads[$fileObj->getName()] = $fileObj->getId();
            $sessionSuccess->$uploadName = $previousUploads;
        }
        return;
    }

    public function getFileObjectListFromUploadName($uploadName) {
        $this->getAppropriateAdapter($uploadName);
        return $this->storageAdapter->fetchAllFromUploadName($uploadName);
    }

    /**
     * 
     * @param string $path
     * @return \Zf3FileUpload\Entity\FileEntityInterface
     */
    public function getFileObjectFromPath($uploadName, $path) {
        $this->getAppropriateAdapter($uploadName);
        $obj = $this->storageAdapter->createFileObjectFromPath($path);
        if (!($obj instanceof \Zf3FileUpload\Entity\FileEntityInterface)) {
            $obj = $this->storageAdapter->fetchObjectFromUploadNameandFileId('', $path);
        }
        return $obj;
    }

    public function getFileObjectFromUploadNameAndFileName($uploadName, $fileId) {
        $this->getAppropriateAdapter($uploadName);
        return $fileObject = $this->storageAdapter->fetchObjectFromUploadNameandFileId($uploadName, $fileId);
    }

    public function removeFileFromUploadNameAndFileId($uploadName, $fileId) {
        $this->getAppropriateAdapter($uploadName);
        $sessionSuccess = new Container('FormUploadSuccessContainer');
        $session = new Container('FormUploadFormContainer');
        $atributes = $session->offsetGet($uploadName);

        if ($atributes['enableRemove'] !== TRUE) {
            return;
        }

        if ($sessionSuccess->offsetExists($uploadName)) {
            $files = $sessionSuccess->$uploadName;
        }

        $remaining = [];

        foreach ($files as $filePath => $fileUuid) {

            if ($fileId == $fileUuid . '') {

                $res = $this->storageAdapter->remove($fileUuid, $filePath);
            } else {
                $remaining[$filePath] = $fileUuid;
            }
        }

        $sessionSuccess->$uploadName = $remaining;
    }

    private function getAppropriateAdapter($uploadName) {
        $session = new Container('FormUploadFormContainer');
        $atributes = $session->offsetGet($uploadName);
        $adapter_keys = array_keys($this->adapters);

        if (isset($atributes['storage']) && in_array($atributes['storage'], $adapter_keys)) {
            $this->storageAdapter = $this->adapters[$atributes['storage']];
        } else {
            $this->storageAdapter = $this->adapters['filesystem'];
        }
    }

}
