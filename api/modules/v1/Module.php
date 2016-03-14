<?php

namespace api\modules\v1;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'api\modules\v1\controllers';

    public function init()
    {
        \Yii::$app->response->headers->set('Access-Control-Allow-Origin', '*');
        parent::init();
    }
}
