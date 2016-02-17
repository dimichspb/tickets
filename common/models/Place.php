<?php

namespace common\Models;

use Yii;


/**
 * This is the model class for table "place".
 *
 * @property integer $id
 * @property string $region
 * @property string $subregion
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
 * @property Region $region0
 * @property Subregion $subregion0
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
            [['parent'], 'integer'],
            [['region', 'subregion', 'city', 'airport'], 'string', 'max' => 3],
            [['country'], 'string', 'max' => 2]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'region' => 'Region',
            'subregion' => 'Subregion',
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
    
    public static function getPlaceByRegionCode($regionCode)
    {
        $place = Place::findOne([
            'region' => $regionCode,
        ]);
        if (!$place) {
            $region = Region::getRegionByCode($regionCode);
            $place = new Place();
            $place->region = $region->code;
            $place->save();
        }

        return $place;
    }
    
    public static function getPlaceBySubregionCode($subregionCode)
    {
        $place = Place::findOne([
            'subregion' => $subregionCode,
        ]);
        if (!$place) {
            $subregion = Subregion::getSubregionByCode($subregionCode);
            $place = new Place();
            $place->subregion = $subregion->code;
            $place->save();
        }

        return $place;
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

    public function getAirports()
    {
        $airportsList = [];
        if ($this->airport) {
            $airport = Airport::getAirportByCode($this->airport);
            $airportsList[] = $airport;
        } elseif ($this->city) {
            $city = City::getCityByCode($this->city);
            $airportsList = $city->getAirports();
        } elseif ($this->country) {
            $country = Country::getCountryByCode($this->country);
            $airportsList = $country->getAirports();
        } elseif ($this->subregion) {
            $subregion = Subregion::getSubregionByCode($this->subregion);
            $airportsList = $subregion->getAirports();
        } elseif ($this->region) {
            $region = Region::getRegionByCode($this->region);
            $airportsList = $region->getAirports();
        }

        return $airportsList;
    }
}
