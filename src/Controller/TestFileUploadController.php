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
        //$this->myForm->setData(['start_date'=>'a044f24f-561a-4dc7-90e1-e43729a8e747,5c639024-25e5-4c55-a1eb-73d0b570b067']);
        //$this->myForm->setData(['start_date'=>'data/UserData/430e2ef6-5ed0-4085-bd4b-9d26d356176c.png,data/UserData/767427b1-40f3-4df0-93fb-df54f46e20fe.png']);
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
