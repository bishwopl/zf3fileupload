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

    public function __construct(ModuleOptions $options, 
            FileUploadService $fileUploadService, 
            ObjectManager $em, 
            MyForm $form)
    {
        $this->options           = $options;
        $this->fileUploadService = $fileUploadService;
        $this->objectManager     = $em;
        $this->myForm            = $form;
    }
    
    public function indexAction()
    {
        //$this->myForm->setData(['start_date'=>'bf5be548-2992-42d7-9dcc-68e6803f6760']);
        //$this->myForm->setData(['start_date'=>'data/UserData/8eacf5de-81b1-41bf-aa9a-44e22ac3262c.png']);
        $vm = new ViewModel();
        $vm->setVariable("form", $this->myForm);
        
        if($this->getRequest()->isPost()){
            $request = $this->getRequest()->getPost()->toArray();
            var_dump($request);
        }
        
        return $vm;
    }
}
