<?php
namespace Zf3FileUpload\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

use Zf3FileUpload\Service\FileUploadService;

class DownloadController extends AbstractActionController
{
    
    protected $moduleOptions;
    protected $uploadName;
    protected $uploadService;

    public function __construct(FileUploadService $uploadService) {
        $this->uploadService = $uploadService;
    }
    
    public function getUploadAction(){
        $uploadName = $this->params()->fromRoute("uploadname");
        $fileName = $this->params()->fromRoute("filename");
        
        if (! $uploadName || !$fileName) {
            return $this->redirect() ->toRoute("home");
        }
        
        $fileObject = $this->uploadService->getFileObjectFromUploadNameAndFileName($uploadName, $fileName);
        
        if($fileObject instanceof \Zf3FileUpload\Entity\FileEntityInterface){
            $mime = $fileObject->getMime();
            $length = $fileObject->getSize();
            $content = $fileObject->getContent();

            if(!is_string($content)){
                $content = stream_get_contents($fileObject->getContent());
            }
            //echo "heasdasasdasdadsdasre"; die();
            
            ob_clean();
            header("Content-Type: $mime"); 
            header("Expires: 0");
            header("Cache-Control: must-revalidate");
            header("Content-Length: $length"); 
            print_r(($content));

            exit;
        }
        
    }

}

