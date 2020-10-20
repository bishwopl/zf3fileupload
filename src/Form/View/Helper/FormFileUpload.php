<?php
namespace Zf3FileUpload\Form\View\Helper;

use Laminas\Form\View\Helper\AbstractHelper;
use Laminas\Form\ElementInterface;
use Laminas\Session\Container;
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
        $vm                   = new \Laminas\Http\PhpEnvironment\Request();
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
                . '<div class="col-xs-6">'
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
                    . 'var minSize = '.$validators['minSize'].';'
                    . 'var maxSize = '.$validators['maxSize'].';'
                    . 'var allowedExtentions = '.json_encode(explode(',', $validators['allowedExtentions'])).';'
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
                            . 'beforeSubmit: function(arr, $form, options) {'
                                . 'var isValid = true;'
                                . 'var selected_files = document.getElementById(\''.$actualUploadButtonId.'\').files;'
                                . 'var msg = "";'
                                . 'for(var i=0; i<selected_files.length; i++){'
                                    . 'var fileName = selected_files[i].name;'
                                    . 'var fileSize = selected_files[i].size;'
                                    . 'var fileExtention = fileName.split(".").pop().toLowerCase();'
                                    . 'if((fileSize<minSize) || (fileSize>maxSize)){'
                                        . 'isValid = false;'
                                        . 'msg += "Size of "+fileName+" is "+humanFileSize(fileSize)+" but must be within "+humanFileSize(minSize)+" to " +humanFileSize(maxSize)+"\n";'
                                    . '}'
                                    . 'if($.inArray(fileExtention, allowedExtentions)===-1){'
                                        . 'isValid = false;'
                                        . 'msg += "File type of "+fileName+" is "+fileExtention+" but must be "+"'. str_replace(',', ' or ', $validators['allowedExtentions']). '"+"\n";'
                                    . '}'
                                . '}'
                                . 'if(!isValid){'
                                    . 'alert("Error(s) occoured: \n\n"+msg+"\n Please try again!");'
                                . '}'
                                . 'return isValid;'
                            . '},'
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
                    . "function humanFileSize(size) {"
                        . "var i = Math.floor( Math.log(size) / Math.log(1024) ); "
                        . "return ( size / Math.pow(1024, i) ).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];"
                    . "}" 
                    . 'function removeUpload(uploadName, fileName, divName){'
                        . 'var url = \''.$this->basepath.'/fileupload/remove-uploaded-file/\'+uploadName+\'/\'+fileName;'
                        . 'var newDivName = "#"+divName;'
                        . '$(newDivName).html(\'<div class="alert alert-warning" style="margin-top:.5em;">'
                        . 'Removing Please wait...'
                        . '</div>\');'                        
                        . '$(newDivName).load(url);'
                        . '$("#'.$responseDivId.'").html("");'
                    . '}'
                . '</script>'
                . '';
    }

    public function setData(ElementInterface $element, $uploadName){
        
        $session = new Container('FormUploadFormContainer');
        $attributes = $element->getAttributes();
        $session->$uploadName = $attributes;
        
        $value = $element->getValue();
        if($value==''){
            return;
        }
        
        $sessionSuccess = new Container('FormUploadSuccessContainer');
        
        $savedFiles = [];
        $fileObjs = [];
        if($sessionSuccess->offsetExists($uploadName)){
            $fileObjs = $this->uploadService->getFileObjectListFromUploadName($uploadName);
        }
        else{
            $files = explode(',', $value);
            foreach ($files as $filenameorid){
                $filenameorid = trim($filenameorid);
                $obj = $this->uploadService->getFileObjectFromPath($uploadName, $filenameorid);
                if($obj instanceof \Zf3FileUpload\Entity\FileEntityInterface){
                    $fileObjs[] = $obj;
                }
            }
        }
        
        foreach ($fileObjs as $f){
            $savedFiles[$f->getName()] = $f->getId();
            
            //this ensures name and id of file remains same after edit so associaions are not broken
            if($attributes['multiple']==FALSE){
                $attributes['newName'] = $f->getName();
                $attributes['newId'] = $f->getId();
            }
        }
        if(sizeof($savedFiles)>0){
            $sessionSuccess->$uploadName = $savedFiles;
        }
        $session->$uploadName = $attributes;
    }
    
    public function __invoke(ElementInterface $element = null) {
        return $this->render($element);
    }
    
    public function reload($uploadName, $atributes){
        $response = '';
        $previewDivId = $uploadName.'previewDivId';
        $validators = $atributes['validator'];
        $downloadDivId = $uploadName.'_downloadDiv';
        $names = [];

        $fileObjects = $this->uploadService->getFileObjectListFromUploadName($uploadName);
        
        foreach($fileObjects as $f){
            $names[] = is_file($f->getName())?$f->getName():$f->getId();
        }

        $response.='<script type="text/javascript">'
            . '$(document).ready(function(){'
            . '$("#'.$uploadName.'_Id'.'").val(\''.implode(',',$names).'\');'
            . '});'
        . '</script>';
        
        if(isset($validators['image'])&&$atributes['showPreview']){
            $previewWidth  = $atributes['preview']['width'];
            $previewHeight = $atributes['preview']['height'];
            $imgs = '';
            foreach($fileObjects as $f){
                $content = $f->getContent();
                if(!is_string($content)){
                    $content = stream_get_contents($content);
                }
                $imgs .= '<img src=\'data:image/png;base64,'.base64_encode($content).'\' '
                        . ' height=\"'.$previewHeight.'\" width=\"'.$previewWidth.'\"/>';
            }
            $response.=''
            . '<script type="text/javascript">'
                . '$(document).ready(function(){'
                    . '$("#'.$previewDivId.'").html("'.$imgs.'");'
                . '});'
            . '</script>'
            . '';
        }
        
        $download = '<table style=\"text-align:center;\" '
                . 'class=\"table table-hover table-responsive table-stripped table-sm\">';
        $total_files = 0;
        foreach($fileObjects as $f){
            $downloadLink = '<a title=\"Get Attachment\" href=\"'
                .$this->basepath.'/fileupload/get-uploaded-file/'.$uploadName.'/'.$f->getId().'\" '
                . 'target=\"_blank\">'
                . '<span class=\"fas fa-download fa-xs\"></span></a>';
            $removeLink = '<span style=\"color: #bb0000; cursor: pointer;\" '
                    . 'title=\"Remove Attachment\" '
                    . 'class=\"fas fa-trash fa-xs\" '
                    . 'onclick=\"removeUpload(\''.$uploadName.'\',\''.$f->getId().'\',\''.$downloadDivId.'\')\">'
                    . '</span>';

            $download .= '<tr>';
                $download .= '<td><span style=\"color: #00bb00; cursor: pointer;\" '
                    . 'title=\"Upload Successful\" '
                    . 'class=\"fas fa-check-circle fa-xs\"'
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
            $ret = substr($name, 0, 5).'...'.substr($name, -9);
        }
        return $ret;
    }
}
