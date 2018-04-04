<?php

/**
 * 
 * @author Bishwo Prasad Lamichhane <bishwo.prasad@gmail.com>
 */
namespace Zf3FileUpload\SizeFormatter;

class SizeFormatter{
    
    public function sizeFormat($size){
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
    
    public function resizeName($name){
        $ret = $name;
        if(strlen($name)>15){
            $ret = substr($name, 0, 5).'...'.substr($name, -6);
        }
        return $ret;
    }
}