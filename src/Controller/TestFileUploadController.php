<?php
namespace Zf3FileUpload\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zf3FileUpload\Service\FileUploadService;
use Zf3FileUpload\ModuleOptions\ModuleOptions;

use Zf3FileUpload\Form\MyForm;

class TestFileUploadController extends AbstractActionController
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;
    
    /**
     * @var \Zf3FileUpload\Service\FileUploadService
     */
    protected $fileUploadService;
    
    /**
     * @var \Zf3FileUpload\ModuleOptions\ModuleOptions
     */
    protected $options;
    
    /**
     * @var \Zf3FileUpload\Form\MyForm
     */
    protected $myForm;

    protected $translator;

    public function __construct(ModuleOptions $options, 
            FileUploadService $fileUploadService, 
            ObjectManager $em, 
            MyForm $form,
            $translator)
    {
        $this->options           = $options;
        $this->fileUploadService = $fileUploadService;
        $this->objectManager     = $em;
        $this->myForm            = $form;
        $this->translator        = $translator;
    }
    
    public function indexAction()
    {
        $vm = new ViewModel();
        $vm->setTemplate("mis-base/form-compact");
        $vm->setVariable("form", $this->myForm);
        
        if($this->getRequest()->isPost()){
            $request = $this->getRequest()->getPost()->toArray();
            var_dump($request);
        }
        
        return $vm;
    }
}
