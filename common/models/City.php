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
 * @property string $region
 * @property string $subregion
 * @property string $country
 *
 * @property Airport[] $airports
 * @property Country $country0
 * @property Region $region0
 * @property Subregion $subregion0
 * @property CityDesc[] $cityDescs
 * @property Language[] $languages
 * @property Place[] $places
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
            [['code', 'region', 'subregion'], 'string', 'max' => 3],
            [['name', 'coordinates', 'time_zone'], 'string', 'max' => 255],
            [['country'], 'string', 'max' => 2]
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
            'region' => 'Region',
            'subregion' => 'Subregion',
            'country' => 'Country',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAirports()
    {
        return $this->hasMany(Airport::className(), ['city' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry0()
    {
        return $this->hasOne(Country::className(), ['code' => 'country']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion0()
    {
        return $this->hasOne(Region::className(), ['code' => 'region']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubregion0()
    {
        return $this->hasOne(Subregion::className(), ['code' => 'subregion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCityDescs()
    {
        return $this->hasMany(CityDesc::className(), ['city' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguages()
    {
        return $this->hasMany(Language::className(), ['code' => 'language'])->viaTable('city_desc', ['city' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlaces()
    {
        return $this->hasMany(Place::className(), ['city' => 'code']);
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
