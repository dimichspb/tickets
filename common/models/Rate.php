<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "rate".
 *
 * @property integer $id
 * @property integer $origin_place
 * @property integer $destination_place
 * @property string $there_date
 * @property string $back_date
 * @property integer $airline
 * @property string $flight_number
 * @property string $currency
 * @property string $price
 *
 * @property Airline $airline0
 * @property Place $originPlace
 * @property Place $destinationPlace
 * @property Place $originPlace0
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
            [['origin_place', 'destination_place', 'there_date', 'airline', 'flight_number', 'currency', 'price'], 'required'],
            [['origin_place', 'destination_place', 'airline'], 'integer'],
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
            'origin_place' => 'Origin Place',
            'destination_place' => 'Destination Place',
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
    public function getOriginPlace()
    {
        return $this->hasOne(Place::className(), ['id' => 'origin_place']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDestinationPlace()
    {
        return $this->hasOne(Place::className(), ['id' => 'destination_place']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOriginPlace0()
    {
        return $this->hasOne(Place::className(), ['id' => 'origin_place']);
    }

    public static function getTodayRateByRouteId($routeId)
    {
        return Rate::findOne([
            'route' => $routeId,
            //todo:: filter today's rates only
        ]);
    }
}
