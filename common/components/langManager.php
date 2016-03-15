<?php

namespace yii\components;

use yii\base\Component;
use yii\base\BootstrapInterface;
use yii\web\Session;
use Yii;

class langManager extends Component implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $session = new Session();

        $app->language = $session->get('language', Yii::$app->params['default_language']);
    }
}