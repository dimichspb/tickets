<?php

namespace common\Models;

use Yii;

/**
 * This is the model class for table "city".
 *
 * @property string $code
 * @property string $name
 * @property string $coordinates
 * @property string $time_zone
 * @property string $country
 *
 * @property Country $country0
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'country'], 'required'],
            [['code', 'name', 'coordinates', 'time_zone', 'country'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'code' => 'Code',
            'name' => 'Name',
            'coordinates' => 'Coordinates',
            'time_zone' => 'Time Zone',
            'country' => 'Country',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry0()
    {
        return $this->hasOne(Country::className(), ['code' => 'country']);
    }

    public static function getCityByCode($cityCode)
    {
        $city = City::findOne([
                'code' => $cityCode,
            ]);
        if (!$city) {
            $city = new City();
            $city->code = $cityCode;
            $city->save();
        }

        return $city;
    }
}
