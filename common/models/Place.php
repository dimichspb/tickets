<?php

namespace common\Models;

use Yii;

/**
 * This is the model class for table "place".
 *
 * @property integer $id
 * @property string $country
 * @property string $city
 * @property string $airport
 * @property integer $parent
 *
 * @property Airport $airport0
 * @property City $city0
 * @property Country $country0
 * @property Place $parent0
 * @property Place[] $places
 */
class Place extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'place';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent'], 'integer'],
            [['country'], 'string', 'max' => 2],
            [['city', 'airport'], 'string', 'max' => 3]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'country' => 'Country',
            'city' => 'City',
            'airport' => 'Airport',
            'parent' => 'Parent',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAirport0()
    {
        return $this->hasOne(Airport::className(), ['code' => 'airport']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity0()
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
    public function getParent0()
    {
        return $this->hasOne(Place::className(), ['id' => 'parent']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlaces()
    {
        return $this->hasMany(Place::className(), ['parent' => 'id']);
    }

    public static function getPlaceByCountryCode($countryCode)
    {
        $place = Place::findOne([
            'country' => $countryCode,
        ]);
        if (!$place) {
            $country = Country::getCountryByCode($countryCode);
            $place = new Place();
            $place->country = $country->code;
            $place->save();
        }

        return $place;
    }

    public static function getPlaceByCityCode($cityCode)
    {
        $place = Place::findOne([
            'city' => $cityCode,
        ]);
        if (!$place) {
            $city = City::getCityByCode($cityCode);
            $country = Country::getCountryByCode($city->country);
            $place = new Place();
            $place->city = $city->code;
            $place->country = $country->code;
            $place->save();
        }

        return $place;
    }

    public static function getPlaceByAirportCode($airportCode)
    {
        $place = Place::findOne([
            'airport' => $airportCode,
        ]);
        if (!$place) {
            $airport = Airport::getAirportByCode($airportCode);
            $city = City::getCityByCode($airport->city);
            $country = Country::getCountryByCode($city->country);
            $place = new Place();
            $place->city = $city->code;
            $place->country = $country->code;
            $place->airport = $airport->code;
            $place->save();
        }

        return $place;
    }
}
