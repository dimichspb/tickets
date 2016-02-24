<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "rate".
 *
 * @property integer $id
 * @property integer $route;
 * @property integer $origin_city
 * @property integer $destination_city
 * @property string $there_date
 * @property string $back_date
 * @property integer $airline
 * @property string $flight_number
 * @property string $currency
 * @property string $price
 * @property string $service
 *
 * @property Airline $airline0
 * @property City $originCity
 * @property City $destinationCity

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
            [['airline'], 'integer'],
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
    public function getAirline0()
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

    public static function getTodayRateByRouteId($routeId)
    {
        return Rate::findOne([
            'route' => $routeId,
            //todo:: filter today's rates only
        ]);
    }
}