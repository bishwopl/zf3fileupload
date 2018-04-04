<?php

namespace Zf3FileUpload;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface {

    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap($e) {
        $sm = $e->getApplication()->getServiceManager();
        $headScript = $sm->get('ViewHelperManager')->get('headScript');
        $headScript->appendScript(file_get_contents(__DIR__ . '/Assets/jquery.form.min.js'), 'text/javascript');
    }

}
