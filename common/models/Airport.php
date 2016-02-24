<?php

namespace common\Models;

use Yii;

/**
 * This is the model class for table "airport".
 *
 * @property string $code
 * @property string $name
 * @property string $coordinates
 * @property string $time_zone
 * @property string $region
 * @property string $subregion
 * @property string $country
 * @property string $city
 *
 * @property City $city0
 * @property Country $country0
 * @property Region $region0
 * @property Subregion $subregion0
 * @property AirportDesc[] $airportDescs
 * @property Language[] $languages
 * @property Place[] $places
 */
class Airport extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'airport';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'country', 'city'], 'required'],
            [['code', 'region', 'subregion', 'city'], 'string', 'max' => 3],
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
            'city' => 'City',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['code' => 'city']);
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
    public function getAirportDescs()
    {
        return $this->hasMany(AirportDesc::className(), ['airport' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguages()
    {
        return $this->hasMany(Language::className(), ['code' => 'language'])->viaTable('airport_desc', ['airport' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlaces()
    {
        return $this->hasMany(Place::className(), ['airport' => 'code']);
    }
    
    public static function getAirportByCode($airportCode)
    {
        $airport = Airport::findOne([
            'code' => $airportCode,
        ]);
        if (!$airport) {
            $airport = new Airport();
            $airport->code = $airportCode;
            $airport->save();
        }

        return $airport;
    }

    public static function getCityByCode($airportCode)
    {
        $airport = Airport::findOne([
            'code' => $airportCode,
        ]);

        if ($airport) {
            return $airport->getCity()->one();
        }
    }
}