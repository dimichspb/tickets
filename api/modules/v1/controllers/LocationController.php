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

        return $behaviors;
    }

    public function actionView()
    {
        return $this->getLocation();
    }

    public function getLocation()
    {
        $url = "http://www.travelpayouts.com/whereami";

        $requestData = [
            'locale' => Yii::$app->language,
        ];

        if (Yii::$app->request->userIP !== '127.0.0.1') {
            $requestData['ip'] = Yii::$app->request->userIP;
        }

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
}