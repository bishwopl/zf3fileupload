<?php
namespace Zf3FileUpload\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

use Zf3FileUpload\Form\MyForm;

class TestFileUploadController extends AbstractActionController
{
   
    /**
     * @var \Zf3FileUpload\Form\MyForm
     */
    protected $myForm;

    public function __construct(MyForm $form)
    {
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
