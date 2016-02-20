<?php

namespace common\Models;

use Yii;

/**
 * This is the model class for table "route".
 *
 * @property integer $id
 * @property string $origin_airport
 * @property string $destination_airport
 * @property string $there_date
 * @property string $back_date
 * @property string $currency
 *
 * @property Airport $destinationAirport
 * @property Airport $originAirport
 */
class Route extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'route';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin_airport', 'destination_airport', 'there_date'], 'required'],
            [['there_date', 'back_date'], 'safe'],
            [['origin_airport', 'destination_airport', 'currency'], 'string', 'max' => 3]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'origin_airport' => 'Origin Airport',
            'destination_airport' => 'Destination Airport',
            'there_date' => 'There Date',
            'back_date' => 'Back Date',
            'currency' => 'Currency',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDestinationAirport()
    {
        return $this->hasOne(Airport::className(), ['code' => 'destination_airport']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOriginAirport()
    {
        return $this->hasOne(Airport::className(), ['code' => 'origin_airport']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency()
    {
        return $this->hasOne(Currency::className(), ['code' => 'currency']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequests()
    {
        return $this->hasMany(Request::className(), ['id' => 'request'])->viaTable('request_to_route', ['route' => 'id']);
    }

    public function getRates()
    {
        return $this->hasMany(Rate::className(), ['route' => 'id']);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $route = Route::findOne([
                'origin_airport' => $this->origin_airport,
                'destination_airport' => $this->destination_airport,
                'there_date' => $this->there_date,
                'back_date' => $this->back_date,
                'currency' => $this->currency,
            ]);
            if (!$route) {
                return parent::beforeSave($insert);
            }
        }
    }

    public static function getRoutesWithOldRate()
    {
        return (Route::find()->select('route.id, count(rate.id) as rates')->leftJoin('rate', 'rate.route=route.id')->groupBy('route.id')->having('rates = 0')->all());
    }

}
