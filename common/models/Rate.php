<?php

namespace common\Models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\Console;
use yii\helpers\CurlHelper;
use yii\components\ProgressBar;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "rate".
 *
 * @property integer $id
 * @property string $create_date
 * @property integer $origin_city
 * @property integer $destination_city
 * @property string $there_date
 * @property string $back_date
 * @property string $flight_number
 * @property string $price
 * @property Route $route
 * @property Airline $airline
 * @property City $originCity
 * @property City $destinationCity
 * @property Currency $currency
 * @property Service $service

 */
class Rate extends \yii\db\ActiveRecord
{
    private static $limit;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['route', 'origin_city', 'destination_city', 'there_date', 'airline', 'flight_number', 'currency', 'price'], 'required'],
            [['origin_city', 'destination_city'], 'string'],
            [['route', 'airline'], 'integer'],
            [['there_date', 'back_date'], 'safe'],
            [['price'], 'number'],
            [['flight_number'], 'string', 'max' => 5],
            [['currency'], 'string', 'max' => 3]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'route' => 'Route',
            'origin_city' => 'Origin City',
            'destination_city' => 'Destination City',
            'there_date' => 'There Date',
            'back_date' => 'Back Date',
            'airline' => 'Airline',
            'flight_number' => 'Flight Number',
            'currency' => 'Currency',
            'price' => 'Price',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAirline()
    {
        return $this->hasOne(Airline::className(), ['id' => 'airline']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOriginCity()
    {
        return $this->hasOne(City::className(), ['code' => 'origin_city']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDestinationCity()
    {
        return $this->hasOne(City::className(), ['code' => 'destination_city']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoute()
    {
        return $this->hasOne(Route::className(), ['id' => 'route']);
    }

    /**
     * Method returns ActiveDataProvided which contains all Rates by the Request ID specified in GET param 'request'
     * for REST API response
     *
     * @return ActiveDataProvider
     */
    public static function getRatesByRequestIdDataProvider()
    {
        $requestId = Yii::$app->request->get('request');
        if (empty($requestId)) return;
        $request = Request::getRequestById($requestId);

        return new ActiveDataProvider([
            'query' => $request->getRates(),
        ]);
    }

    /**
     * Method gets Rate data from providers by the specified $requestId. Number of requests is limited by $limit
     *
     * @param $requestId
     * @param $limit
     */
    public static function getRates($requestId, $limit)
    {
        Rate::setLimit($limit);
        if ($requestId) {
            $routesToUpdate = Route::getRoutesWithOldRateByRequestId($requestId);
        } else {
            $routesToUpdate = Route::getRoutesWithOldRate();
        }

        $activeRateService = ServiceType::directFlights();

        if (!$activeRateService || !$routesToUpdate) {
            return;
        }

        foreach ($activeRateService->endpoints  as $endpoint) {
            Rate::getRatesFromService($endpoint, $routesToUpdate);
        }
    }

    /**
     * Method gets Rates data of the specified routes which need to be updated from the specified $endpoint
     *
     * @param Endpoint $endpoint
     * @param array $routesToUpdate
     */
    private static function getRatesFromService(Endpoint $endpoint, array $routesToUpdate)
    {
        switch ($endpoint->service) {
            case 'AVS':
                Rate::getRatesFromAVS($endpoint, $routesToUpdate);
                break;
            default:
        }
    }

    /**
     * Method gets Rates data from AVS service
     *
     * @param Endpoint $endpoint
     * @param array $routesToUpdate
     */
    private static function getRatesFromAVS(Endpoint $endpoint, array $routesToUpdate)
    {
        foreach ($routesToUpdate as $routeToUpdate) {

            $curlAction = CurlHelper::get($endpoint->endpoint, [
                'currency' => $routeToUpdate->currency,
                'origin' => $routeToUpdate->origin_city,
                'destination' => $routeToUpdate->destination_city,
                'depart_date' => \Yii::$app->formatter->asDate($routeToUpdate->there_date, 'php:Y-m-d'),
                'return_date' => \Yii::$app->formatter->asDate($routeToUpdate->back_date, 'php:Y-m-d'),
                'token' => $endpoint->getService()->token,
            ]);

            $responseJson = $curlAction['response'];
            $responseCode = $curlAction['responseCode'];

            if ($responseCode !== 200) {
                continue;
            }
            Rate::addRatesAVS($endpoint, $routeToUpdate, $responseJson);

        }
    }


    /**
     * Method adds Rates to DB from the provided JSON data based on AVS response structure
     *
     * @param Endpoint $endpoint
     * @param Route $route
     * @param $dataJson
     */
    private static function addRatesAVS(Endpoint $endpoint, Route $route, $dataJson)
    {
        $data = Json::decode($dataJson);

        foreach ($data['data'] as $destinationItemIndex => $destinationItemData) {
            foreach ($destinationItemData as $destinationDataItem) {
                $rate = new Rate();
                $rate->route = $route->id;
                $rate->origin_city = $route->origin_city;
                $rate->destination_city = $destinationItemIndex;
                $rate->there_date = \Yii::$app->formatter->asDatetime($destinationDataItem['departure_at'],'php:Y-m-d H:i:s');
                $rate->back_date = \Yii::$app->formatter->asDatetime($destinationDataItem['return_at'],'php:Y-m-d H:i:s');
                $rate->service = $endpoint->service;
                $rate->airline = Airline::getAirlineByName($destinationDataItem['airline'])->id;
                $rate->flight_number = (string)$destinationDataItem['flight_number'];
                $rate->currency = $route->currency;
                $rate->price = (float)$destinationDataItem['price'];

                if ($rate->validate() && $rate->save()) {
                    Rate::checkLimit();
                }
            }

        }
    }

    /**
     * Method sets the limit of requests to providers
     *
     * @param $limit
     */
    private static function setLimit($limit)
    {
        self::$limit = $limit;
    }

    /**
     * Method checks whether limit is exceed
     */
    private static function checkLimit()
    {
        if (isset(self::$limit)) {
            if (--self::$limit <= 0) exit();
        }
    }
}
