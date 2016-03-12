<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use common\models\Place;
use yii\data\ActiveDataProvider;


class PlaceController extends ActiveController
{
    public $modelClass = 'common\models\Place';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON; //setting JSON as default reply

        return $behaviors;
    }


    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [new $this->modelClass, 'getPlacesByStringDataProvider']; // replacing default DataProvider
        return $actions;
    }
}