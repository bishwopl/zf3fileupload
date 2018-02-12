<?php
namespace Zf3FileUpload\Form\View\Helper;

use Zf3FileUpload\Form\Element;
use Zend\Form\View\Helper\FormElement as BaseFormElement;
use Zend\Form\ElementInterface;
  
class FormElement  extends BaseFormElement
{
    public function render(ElementInterface $element)
    {
        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }

        if ($element instanceof Element\FileUpload) {
            $helper = $renderer->plugin('formFileUpload');
            return $helper($element);
        }

        return parent::render($element);
    }
}