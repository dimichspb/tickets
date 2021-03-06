<?php

namespace common\models;

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
            'route' => Yii::t('app', 'Route'),
            'origin_city' => Yii::t('app', 'Origin city'),
            'destination_city' => Yii::t('app', 'Destination city'),
            'there_date' => Yii::t('app', 'There Date'),
            'back_date' => Yii::t('app', 'Back Date'),
            'airline' => Yii::t('app', 'Airline'),
            'flight_number' => Yii::t('app', 'Flight Number'),
            'currency' => Yii::t('app', 'Currency'),
            'price' => Yii::t('app', 'Price'),
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
     * @return Airline
     */
    public function getAirlineOne()
    {
        return $this->getAirline()->one();
    }

    public function getAirlineName()
    {
        return $this->getAirlineOne()->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOriginCity()
    {
        return $this->hasOne(City::className(), ['code' => 'origin_city']);
    }

    /**
     * @return City
     */
    public function getOriginCityOne()
    {
        return $this->getOriginCity()->one();
    }

    /**
     * @param Language $language
     * @return string
     */
    public function getOriginCityName(Language $language = null)
    {
        if (!$language) {
            $language = Language::getDefaultLanguage();
        }
        return $this->getOriginCityOne()->getCityDescByLanguage($language)->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDestinationCity()
    {
        return $this->hasOne(City::className(), ['code' => 'destination_city']);
    }

    /**
     * @return City
     */
    public function getDestinationCityOne()
    {
        return $this->getDestinationCity()->one();
    }

    /**
     * @param Language $language
     * @return string
     */
    public function getDestinationCityName(Language $language = null)
    {
        if (!$language) {
            $language = Language::getDefaultLanguage();
        }
        return $this->getDestinationCityOne()->getCityDescByLanguage($language)->name;
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
    public static function process($requestId, $limit = 1000)
    {
        Rate::setLimit($limit);
        if ($requestId) {
            $routesToUpdate = Route::getRoutesWithOldRateByRequestId($requestId, $limit);
        } else {
            $routesToUpdate = Route::getRoutesWithOldRate($limit);
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
     * @param Route[] $routesToUpdate
     */
    private static function getRatesFromAVS(Endpoint $endpoint, array $routesToUpdate)
    {
        //Console::stdout("Total routes to update: " . count($routesToUpdate) . PHP_EOL);
        //$i = 0;
        $today = new \DateTime();
        foreach ($routesToUpdate as $routeToUpdate) {
            //Console::stdout("Route: " . $routeToUpdate->id . ', ' . $i++);
            $thereDate = new \DateTime($routeToUpdate->there_date);
            $backDate = new \DateTime($routeToUpdate->back_date);
            if ($thereDate <= $today || $backDate <= $today) {
                //Console::stdout('out of date' . PHP_EOL);
                $routeToUpdate->status = 1;
                $routeToUpdate->save();
                continue;
            }

            $requestData = [
                'currency' => $routeToUpdate->currency,
                'origin' => $routeToUpdate->origin_city,
                'destination' => $routeToUpdate->destination_city,
                'depart_date' => \Yii::$app->formatter->asDate($routeToUpdate->there_date, 'php:Y-m-d'),
                'return_date' => \Yii::$app->formatter->asDate($routeToUpdate->back_date, 'php:Y-m-d'),
                'token' => $endpoint->getService()->token,
            ];

            $curlAction = CurlHelper::get($endpoint->endpoint, $requestData);

            $responseJson = $curlAction['response'];
            $responseCode = $curlAction['responseCode'];

            if ($responseCode !== 200) {
                continue;
            }
            Rate::addRatesAVS($endpoint, $routeToUpdate, $responseJson);
            //Console::stdout(PHP_EOL);
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
        $log = 'Route: ' . $route->id . '...';
        $route->last_update = (new \DateTime())->format('Y-m-d H:i:s');
        $route->update(false, ['last_update']);
        $data = Json::decode($dataJson);

        if (count($data['data']) === 0) {
            //$route->status = 1;
            //$route->save();
            return;
        }

        foreach ($data['data'] as $destinationItemIndex => $destinationItemData) {
            foreach ($destinationItemData as $destinationDataItem) {
                $rate = new Rate();
                $rate->route = $route->id;
                $rate->origin_city = $route->origin_city;
                $rate->destination_city = $destinationItemIndex;
                $rate->there_date = \Yii::$app->formatter->asDatetime($destinationDataItem['departure_at'],'php:Y-m-d H:i:s');
                $rate->back_date = \Yii::$app->formatter->asDatetime($destinationDataItem['return_at'],'php:Y-m-d H:i:s');
                $rate->service = $endpoint->service;
                $rate->airline = Airline::getAirlineByIATA($destinationDataItem['airline'])->id;
                $rate->flight_number = (string)$destinationDataItem['flight_number'];
                $rate->currency = $route->currency;
                $rate->price = (float)$destinationDataItem['price'];

                if ($rate->validate() && $rate->save()) {
                    $log .= 'Rate ' . $rate->id . ', ';
                    Rate::checkLimit();
                }
            }
        }
        Console::stdout($log . PHP_EOL);
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
