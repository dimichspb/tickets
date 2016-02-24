<?php

namespace common\Models;

use Yii;

/**
 * This is the model class for table "route".
 *
 * @property integer $id
 * @property string $origin_city
 * @property string $destination_city
 * @property string $there_date
 * @property string $back_date
 * @property string $currency
 *
 * @property City $destinationCity
 * @property City $originCity
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
            [['origin_city', 'destination_city', 'there_date'], 'required'],
            [['there_date', 'back_date'], 'safe'],
            [['origin_city', 'destination_city', 'currency'], 'string', 'max' => 3]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'origin_city' => 'Origin City',
            'destination_city' => 'Destination City',
            'there_date' => 'There Date',
            'back_date' => 'Back Date',
            'currency' => 'Currency',
        ];
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
    public function getOriginCity()
    {
        return $this->hasOne(City::className(), ['code' => 'origin_city']);
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
                'origin_city' => $this->origin_city,
                'destination_city' => $this->destination_city,
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
        $query = (Route::find()->select('route.*, count(rate.id) as rates')->leftJoin('rate', 'rate.route=route.id AND DATE(rate.create_date) = CURDATE()')->groupBy('route.id')->having('rates = 0'));

        $result = $query->all();

        if (count($result) > 0) {
            return($result);
        }
    }

}
