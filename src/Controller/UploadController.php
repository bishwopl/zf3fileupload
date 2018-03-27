<?php
namespace Zf3FileUpload\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

use Zf3FileUpload\Service\FileUploadService;
class UploadController extends AbstractActionController
{
    protected $moduleOptions;
    
    protected $uploadName;
    
    /**
     * @var \Zf3FileUpload\Service\FileUploadService 
     */
    protected $uploadService;
    
    /**
     * @var FileUpload\Entity\FileEntityInterface 
     */
    protected $fileEntity;

    public function __construct(FileUploadService $uploadService) {
        $this->uploadService = $uploadService;
        $this->fileEntity = $this->uploadService->getFileObject();
    }

    public function indexAction(){
        $vm = new ViewModel();
        $vm->setTerminal(true);
        $request = $this->getRequest();
        $errors = [];
        
        $files = $request->getFiles()->toArray();

        $uploadName = array_keys($files)[0];
        $session = new Container('FormUploadFormContainer');
        $attributes = $session->offsetGet($uploadName);
        $this->uploadName = $uploadName;
        
        $validators = $attributes['validator'];
        $uploadDir = $attributes['uploadDir'];
        $buttonId = $attributes['id']!==''?$attributes['id'].'__button':'upload__button';
        
        $form = $this->uploadService->getForm($uploadName);
        $filter = $this->uploadService->getInutFilter($validators, $uploadName);
        $form->setData($files);
        $form->setInputFilter($filter);
        
        $vm->setVariable('buttonId', $buttonId);
        $vm->setVariable('buttonSuccessText',$attributes['successText']!==''?$attributes['successText']:$attributes['uploadText']);
        $vm->setVariable('buttonSuccessIcon', $attributes['successIcon']);
        $vm->setVariable('buttonErrorText', $attributes['errorText']!==''?$attributes['errorText']:$attributes['uploadText']);
        $vm->setVariable('buttonErrorIcon', $attributes['errorIcon']);
        
        if(!$form->isValid()){
            $errors = $form->getInputFilter()->getMessages();
            $vm->setVariable('errors', $errors);
            return $vm;
        }
        
        if(!is_dir($uploadDir)){
            $this->uploadService->createDestinationDirectory($uploadDir);
        }
        
        $uploadObj = new \Zend\File\Transfer\Adapter\Http(); 
        $uploadObj->setDestination($uploadDir);
        
        if($uploadObj->receive()){
            $this->uploadService->removePreviousUploads($attributes, $uploadName);
            $originalNames = $uploadObj->getFileName();
            if(!is_array($originalNames)){
                $originalNames = [$originalNames];
            }
            
            if(isset($attributes['crop'])){
                $this->uploadService->cropImage($attributes['crop'], $originalNames);
            }
            
            foreach($originalNames as $f){
                //files are stored in filesystem by default
                //if db storage is enabled then file is deleted after it is inserted in db
                $f = str_replace('\\', '/', $f);
                $this->uploadService->storeFile($f, $attributes, $uploadName);
            }
            
            $fileObjects = $this->uploadService->getFileObjectListFromUploadName($uploadName);

            $this->uploadService->callBack($attributes['callback'], $fileObjects);
            
            $previewDivId = $uploadName.'previewDivId';
            $vm->setVariable('previewDiv', $previewDivId);
            $vm->setVariable('isImage', isset($validators['image']));
            $vm->setVariable('showPreview', $attributes['showPreview']);
            $vm->setVariable('previewDim', $attributes['preview']);
            $vm->setVariable('inputId', $uploadName.'_Id');
            $vm->setVariable('downloadDiv', $uploadName.'_downloadDiv');
            $vm->setVariable('uploadName', $uploadName); 
            $vm->setVariable('enableRemove', $attributes['enableRemove']); 
            $vm->setVariable('files', $fileObjects);
        }
        else{
            //cannot save uploaded files
        }
        return $vm;
    }
}

