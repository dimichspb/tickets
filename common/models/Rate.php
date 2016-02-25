<?php

namespace common\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\CurlHelper;
use yii\components\ProgressBar;
use yii\helpers\Json;

/**
 * This is the model class for table "rate".
 *
 * @property integer $id
 * @property integer $origin_city
 * @property integer $destination_city
 * @property string $there_date
 * @property string $back_date
 * @property string $flight_number
 * @property string $price
 *
 * @property Route $route
 * @property Airline $airline
 * @property City $originCity
 * @property City $destinationCity
 * @property Currency $currency
 * @property Service $service

 */
class Rate extends \yii\db\ActiveRecord
{
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
        return $this->hasOne(City::className(), ['city' => 'destination_city']);
    }


    public function getRoute()
    {
        return $this->hasOne(Route::className(), ['id' => 'route']);
    }


    public static function getRatesByRequestId($requestId)
    {
        $result = Rate::find()->innerJoin('request_to_route', [
            'request_to_route.route' => new Expression('`rate`.`route`'),
            'request_to_route.request' => $requestId])->orderBy('price');

        return $result;
    }

    public static function getRatesByRequestIdDataProvider()
    {
        $requestId = Yii::$app->request->get('request');

        return new ActiveDataProvider([
            'query' => Rate::getRatesByRequestId($requestId),
        ]);
    }



    public static function getRates()
    {
        $routesToUpdate = Route::getRoutesWithOldRate();

        $activeRateService = ServiceType::findOne([
            'status' => ServiceType::STATUS_ACTIVE,
            'code' => 'DR',
        ]);

        if (!$activeRateService || !$routesToUpdate) {
            return;
        }

        foreach ($activeRateService->endpoints  as $endpoint) {
            Rate::getRatesFromService($endpoint, $routesToUpdate);
        }
    }

    private static function getRatesFromService(Endpoint $endpoint, array $routesToUpdate)
    {
        switch ($endpoint->service) {
            case 'AVS':
                Rate::getRatesFromAVS($endpoint, $routesToUpdate);
                break;
            default:
        }
    }

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

                if ($rate->validate()) {
                    $rate->save();
                }
            }

        }

    }
}
