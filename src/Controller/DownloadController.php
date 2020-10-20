<?php
namespace Zf3FileUpload\Controller;

use Laminas\Mvc\Controller\AbstractActionController;

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
            $content = $fileObject->getContent();

            if(!is_string($content)){
                $content = stream_get_contents($fileObject->getContent());
            }
            
            ob_clean();
            header("Content-Type: ".$fileObject->getMime()); 
            header("Expires: 0");
            header("Cache-Control: must-revalidate");
            header("Content-Length: ".$fileObject->getSize()); 
            print_r(($content));

            exit;
        }
        
    }

}

