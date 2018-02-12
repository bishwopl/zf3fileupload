<?php
namespace Zf3FileUpload\Form\View\Helper;

use Zend\Form\View\Helper\AbstractHelper;
use Zend\Form\ElementInterface;
use Zend\Session\Container;
use Zf3FileUpload\Service\FileUploadService;
 
class FormFileUpload extends AbstractHelper {
    
    protected $basepath;
    
    /**
     *
     * @var \Zf3FileUpload\Service\FileUploadService
     */
    protected $uploadService;
    
    public function __construct(FileUploadService $uploadService) {
        $this->uploadService = $uploadService;
        $vm                   = new \Zend\Http\PhpEnvironment\Request();
        $this->basepath       = $vm->getBaseUrl();
    }

    public function render(ElementInterface $element) {
        $attr = $element->getAttributes();
        $attrHtml = '';
        $multiple = $element->getAttribute('multiple')==true?' multiple ':'';
        $label = $element->getAttribute('uploadText')!==''?$element->getAttribute('uploadText'):'Upload';
        
        foreach ($attr as $key => $value){
            if(!is_array($value)){
                $attrHtml.=' '.$key.'="'.$value.'" ';
            }
        }
        
        $field_name = $element->getAttribute('name')!==''?$element->getAttribute('name'):'';
        $formUniqueId = $attr['formUniqueId'];
        $uploadName = str_replace('[', '_', $field_name).'___';
        $uploadName = str_replace(']', '_', $uploadName).$formUniqueId;
        $downloadDiv = $uploadName.'_downloadDiv';
        
        
        $uploadNameId = $uploadName.'_Id';
        $responseDivId = $uploadName.'reponseDivId';
        $previewDivId = $uploadName.'previewDivId';
        $buttonId = $element->getAttribute('id')!==''?$element->getAttribute('id').'__button':'upload__button';
        $progressContainerId = 'progressContainerId'.$buttonId;
        $progressId = 'progressId'.$buttonId;
        $random = rand(1000,9999999);
        $actualUploadButtonId = $buttonId.$random;
        $actualUploadformId = $actualUploadButtonId.'_form';
        $addedFormDivId = $actualUploadformId.'_div';
        $uploading = $element->getAttribute('uploadingText')!==''?$element->getAttribute('uploadingText'):'Uploading';
        
        $this->setData($element, $uploadName);
        
        $validators = $attr['validator'];
        $helpText = 'Min Size : '.  $this->sizeFormat($validators['minSize']).', '
                . 'Max Size : '.$this->sizeFormat($validators['maxSize']).', '
                . 'Allowed Types : '.$validators['allowedExtentions'];
        
        return '<div class="row">'
                . '<div class="col-sm-6">'
                . '<input type="hidden" name="'.$field_name.'" id="'.$uploadNameId.'">'
                . '<div id="'.$previewDivId.'"></div>'
                . '<button type="button" data-loading-text="'.$uploading.'" '
                . 'class= "'.$element->getAttribute('class').'" id="'.$buttonId.'" '
                . 'title="'.$helpText.'" data-html="true" data-toggle="tooltip" data-placement="top"> '
                . '<span class="'.$element->getAttribute('icon').'"></span> '
                . $label
                . '</button><div class="assassass" id="'.$downloadDiv.'"></div>'
                . '<div class="progress progress-striped active" id="'.$progressContainerId.'" style="display:none;">'
                . '<div class="progress-bar text-center" id="'.$progressId.'" style="width: 0%">'
                . '0%</div></div></div></div>'
                . '<div id="'.$responseDivId.'" style="display:none;"></div>'
                . '<script type="text/javascript">'
                . '$(document).ready(function(){'
                    . '$(\'#'.$buttonId.'\').click(function(){'
                        . 'if ($(\'#'.$addedFormDivId.'\').length == 0) {'
                            . '$("body").append(\''
                            . '<div id="'.$addedFormDivId.'" style="display:none;">'
                                . '<form action="'.$this->basepath.'/fileupload/upload" '
                                . 'method="POST" id="'.$actualUploadformId.'" '
                                . 'name="'.$actualUploadformId.'" enctype="multipart/form-data">'
                                    . '<input type="file" '.$multiple.' '
                                    . 'id="'.$actualUploadButtonId.'" name="'.$uploadName.'[]">'
                                    . '<button id="q12">Test</button>'
                                . '</form>'
                            . '</div>\');'
                        . '}'
                        . '$( "#'.$actualUploadButtonId.'" ).trigger( "click" );'
                    . '});'
                    . '$( document ).on( \'change\', \'#'.$actualUploadButtonId.'\', function() { '
                        . '$(\'#'.$actualUploadformId.'\').ajaxForm({'
                            . 'beforeSend: function() {'
                                . '$(\'#'.$buttonId.'\').button(\'loading\');'
                                . 'var percentVal = \'0%\';'
                                . '$("#'.$progressContainerId.'").show();'
                                . '$(\'#'.$progressId.'\').css(\'width\', \'0%\');'
                                . '$(\'#'.$progressId.'\').html(\'0%\');'
                            . '},'
                            . 'uploadProgress: function(event, position, total, percentComplete) {'
                                . 'var percentVal = percentComplete + \'%\';'
                                . '$(\'#'.$progressId.'\').css(\'width\', percentVal);'
                                . '$(\'#'.$progressId.'\').html(percentVal);'
                            . '},'
                            . 'success: function() {'
                                . 'var percentVal = \'100%\';'
                                . '$(\'#'.$progressId.'\').css(\'width\', percentVal);'
                                . '$(\'#'.$progressId.'\').html(percentVal);'
                            . '},'
                            . 'complete: function(xhr) {'
                                . '$(\'#'.$buttonId.'\').button(\'reset\');'
                                . '$("#'.$progressContainerId.'").hide();'
                                . '$("#'.$responseDivId.'").show();'
                                . '$("#'.$responseDivId.'").html(xhr.responseText);'
                            . '}'
                        . '});'
                        . '$( "#'.$actualUploadformId.'" ).trigger("submit");'
                    . '});'
                . '});'
                . '</script>'.$this->reload($uploadName, $attr)
                . '<script type="text/javascript">'
                    . 'function removeUpload(uploadName, fileName, divName){'
                        . 'var url = \''.$this->basepath.'/fileupload/remove-uploaded-file/\'+uploadName+\'/\'+fileName;'
                        . 'var newDivName = "#"+divName;'
                        . '$(newDivName).html(\'<div class="alert alert-warning" style="margin-top:.5em;">'
                        . 'Removing Please wait...'
                        . '</div>\');'                        
                        .'$(newDivName).load(url);'
                    . '}'
                . '</script>'
                . '';
    }

    public function setData(ElementInterface $element, $uploadName){
        $value = $element->getValue();
        if($value==''){
            return;
        }
        $sessionSuccess = new Container('FormUploadSuccessContainer');
        $savedFiles = [];
        $fileObjs = $this->uploadService->getFileObjectListFromUploadName($uploadName);
        foreach ($fileObjs as $f){
            $savedFiles[] = $f->getFileId();
        }
        if(sizeof($savedFiles)>0){
            $sessionSuccess->$uploadName = $savedFiles;
        }
    }
    
    public function __invoke(ElementInterface $element = null) {
        $session = new Container('FormUploadFormContainer');
        $field_name = $element->getAttribute('name')!==''?$element->getAttribute('name'):'';
        $formUniqueId = $element->getAttribute('formUniqueId');
        $uploadName = str_replace('[', '_', $field_name).'___';
        $uploadName = str_replace(']', '_', $uploadName).$formUniqueId;
        
        
        $session->$uploadName = $element->getAttributes();
        return $this->render($element);
    }
    
    public function reload($uploadName, $atributes){
        $response = '';
        $previewDivId = $uploadName.'previewDivId';
        $validators = $atributes['validator'];
        $downloadDivId = $uploadName.'_downloadDiv';
        $sessionSuccess = new Container('FormUploadSuccessContainer');

        $fileObjects = $this->uploadService->getFileObjectListFromUploadName($uploadName);
        
        $names = [];
        foreach($fileObjects as $f){
            if($f instanceof \Zf3FileUpload\Entity\FileEntityInterface){
                $names[] = $f->getFileId();
            }
        }
        
        $sessionSuccess->$uploadName = $names;
        
        if(isset($validators['image'])&&$atributes['showPreview']){
            $previewWidth  = $atributes['preview']['width'];
            $previewHeight = $atributes['preview']['height'];
            $imgs = '';
            $names = [];
            foreach($fileObjects as $f){
                $content = $f->getContent();
                if(!is_string($content)){
                    $content = stream_get_contents($content);
                }
                $imgs .= '<img src=\'data:image/png;base64,'.base64_encode($content).'\' '
                        . ' height=\"'.$previewHeight.'\" width=\"'.$previewWidth.'\"/>';
                $names[] = $f->getFileId();
            }
            $response.=''
            . '<script type="text/javascript">'
                . '$(document).ready(function(){'
                    . '$("#'.$previewDivId.'").html("'.$imgs.'");'
                . '});'
            . '</script>'
            . '';
        }
        $response.='<script type="text/javascript">'
            . '$(document).ready(function(){'
                . '$("#'.$uploadName.'_Id'.'").val(\''.implode(',',$names).'\')'
            . '});'
        . '</script>';
        $download = '<table style=\"text-align:center;\" '
                . 'class=\"table table-hover table-responsive table-stripped table-bordered\">';
        $total_files = 0;
        foreach($fileObjects as $f){
            $downloadLink = '<a title=\"Get Attachment\" href=\"'
                .$this->basepath.'/fileupload/get-uploaded-file/'.$uploadName.'/'.basename($f->getFileId()).'\" '
                . 'target=\"_blank\">'
                . '<span class=\"glyphicon glyphicon-download-alt\"></span></a>';
            $removeLink = '<span style=\"color: #bb0000; cursor: pointer;\" '
                    . 'title=\"Remove Attachment\" '
                    . 'class=\"glyphicon glyphicon-trash\" '
                    . 'onclick=\"removeUpload(\''.$uploadName.'\',\''.basename($f->getFileId()).'\',\''.$downloadDivId.'\')\">'
                    . '</span>';

            $download .= '<tr>';
                $download .= '<td><span style=\"color: #00bb00; cursor: pointer;\" '
                    . 'title=\"Upload Successful\" '
                    . 'class=\"glyphicon glyphicon-ok\"'
                    . '</span></td>';
                $download .= '<td><small>'.$this->resizeName(basename($f->getName())).'</small></td>';
                $download .= '<td><small>'.$this->sizeFormat($f->getSize()).'</small></td>';
                $download .= '<td>'.$downloadLink.'</td>';
                if($atributes['enableRemove']==TRUE){
                    $download .= '<td>'.$removeLink.'</td>';
                }
            $download .= '</tr>';
            $total_files++;
        }
        $download .= '</table>'; 
        if($total_files>0){
            $response.=''
            . '<script type="text/javascript">'
            . '$(document).ready(function(){'
                . '$("#'.$downloadDivId.'").html("'.$download.'");'
            . '});'
            . '</script>'
            . '';
        }
        return $response;
    }
    
    protected function sizeFormat($size){
        $sizetext = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        for($i=0; $i<sizeof($sizetext); $i++){
            if($size<(pow(1024,($i+1)))){
                $modified = round($size/(pow(1024,($i))),1);
                return $modified.' '.$sizetext[$i];
            }
        }
        $modified = round($size/(pow(1024,($i))),1);
        return $modified.' '.$sizetext[$i];
    }
    
    protected function resizeName($name){
        $ret = $name;
        if(strlen($name)>15){
            $ret = substr($name, 0, 5).'...'.substr($name, -6);
        }
        return $ret;
    }
}