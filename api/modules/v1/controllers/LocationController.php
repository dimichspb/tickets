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
            'cors' => [
                'Origin' => ['http://www.biletracker.com', 'http://biletracker.com', 'http://www.biletracker.ru', 'http://biletracker.ru'],
            ],
        ];

        return $behaviors;
    }

    public function actionView()
    {
        return $this->getLocation();
    }

    public function getLocation()
    {
        $url = "http://www.travelpayouts.com/whereami";

        $requestData = [];
        //$requestData = [
        //    'locale' => Yii::$app->language,
        //];

       // if (Yii::$app->request->userIP !== '127.0.0.1') {
            $requestData['ip'] = $this->getIP();
       // }

        var_dump($_SERVER);
        var_dump($requestData);

        $curlAction = CurlHelper::get($url, $requestData);

        $responseJson = $curlAction['response'];
        $responseCode = $curlAction['responseCode'];


        if ($responseCode !== 200) {
            return;
        }
        $json = json_decode($responseJson);
        var_dump($json);
        $iata = $json->iata;

        $airport = Airport::getAirportByCode($iata);
        var_dump($airport);
        if (!$airport) {
            return;
        }

        $place = Place::getPlaceByAirportCode($airport->code);
        var_dump($place);
        if (!$place) {
            return;
        }
        die();
        return $place->attributes();
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