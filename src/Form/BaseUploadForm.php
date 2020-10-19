<?php
namespace Zf3FileUpload\Form;
 
use Laminas\Form\Form;
 
class BaseUploadForm extends Form
{
    public function __construct($name)
    {
        parent::__construct($name);
         
        $this->add(array(
            'name' => $name,
            'attributes' => array(
                'type'     => 'file',
                'multiple' =>'true'
            ),
        )); 
    }
}