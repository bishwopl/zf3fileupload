<?php

namespace Zf3FileUpload;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface {

    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap($e) {
        $sm = $e->getApplication()->getServiceManager();
        
        $inlineScript = $sm->get('ViewHelperManager')->get('inlineScript');
        $inlineScript->appendScript(file_get_contents(__DIR__ . '/Assets/jquery.form.min.js'), 'text/javascript');
        
        $headStyle = $sm->get('ViewHelperManager')->get('headStyle');
        $headStyle->appendStyle(file_get_contents(__DIR__ . '/Assets/font-awesome-all.min.css'));
    }

}
