<?php

namespace api\modules\v1\controllers;

use common\models\Airport;
use common\models\Place;
use Yii;
use yii\rest\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use common\models\Rate;
use yii\helpers\CurlHelper;


class LocationController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON; //setting JSON as default reply
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
        ];

        return $behaviors;
    }

    public function actionView($iata)
    {
        return json_encode((object)$this->getLocation($iata));
    }

    public function getLocation($iata)
    {
        $airport = Airport::getAirportByCode($iata);
        if (!$airport) {
            return;
        }

        $place = Place::getPlaceByAirportCode($airport->code);
        if (!$place) {
            return;
        }

        return $place->getAttributes();
    }

    public function getIP()
    {

        if (getenv("HTTP_CLIENT_IP")) $ip = getenv("HTTP_CLIENT_IP");
        else if(getenv("HTTP_X_FORWARDED_FOR")) $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if(getenv("REMOTE_ADDR")) $ip = getenv("REMOTE_ADDR");
        else $ip = "UNKNOWN";

        return $ip;

    }
}