<?php

namespace common\Models;

use Yii;


/**
 * This is the model class for table "place".
 *
 * @property integer $id
 *
 * @property Airport $airport
 * @property City $city
 * @property Country $country
 * @property Place $parent
 * @property Place[] $places
 * @property Region $region
 * @property Subregion $subregion
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
    public function getAirport()
    {
        return $this->hasOne(Airport::className(), ['code' => 'airport']);
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
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['code' => 'country']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
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
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['code' => 'region']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubregion()
    {
        return $this->hasOne(Subregion::className(), ['code' => 'subregion']);
    }

    /**
     * Method returns Place object by the specified $regionCode
     *
     * @param $regionCode
     * @return Place|null
     */
    public static function getPlaceByRegionCode($regionCode)
    {
        $place = Place::findOne([
            'region' => $regionCode,
        ]);

        return $place;
    }

    /**
     * Method returns Place object by the specified $subregionCode
     *
     * @param $subregionCode
     * @return Place|null
     */
    public static function getPlaceBySubregionCode($subregionCode)
    {
        $place = Place::findOne([
            'subregion' => $subregionCode,
        ]);

        return $place;
    }

    /**
     * Method returns Place object by the specified $countryCode
     *
     * @param $countryCode
     * @return Place|null
     */
    public static function getPlaceByCountryCode($countryCode)
    {
        $place = Place::findOne([
            'country' => $countryCode,
        ]);

        return $place;
    }

    /**
     * Method returns Place object by the specified $cityCode
     *
     * @param $cityCode
     * @return Place|null
     */
    public static function getPlaceByCityCode($cityCode)
    {
        $place = Place::findOne([
            'city' => $cityCode,
        ]);

        return $place;
    }

    /**
     * Method returns Place object by the specified $airportCode
     *
     * @param $airportCode
     * @return Place|null
     */
    public static function getPlaceByAirportCode($airportCode)
    {
        $place = Place::findOne([
            'airport' => $airportCode,
        ]);

        return $place;
    }

    /**
     * Method returns the list of all Airports of the Place and its children
     *
     * @return array|Airport[]
     */
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

    /**
     * Method returns the list of all Cities of the Place and its children
     *
     * @return array|City[]
     */
    public function getCities()
    {
        $citiesList = [];
        if ($this->airport) {
            $city = Airport::getCityByCode($this->airport);
            $citiesList[] = $city;
        } elseif ($this->city) {
            $city = City::getCityByCode($this->city);
            $citiesList[] = $city;
        } elseif ($this->country) {
            $country = Country::getCountryByCode($this->country);
            $citiesList = $country->getCities();
        } elseif ($this->subregion) {
            $subregion = Subregion::getSubregionByCode($this->subregion);
            $citiesList = $subregion->getCities();
        } elseif ($this->region) {
            $region = Region::getRegionByCode($this->region);
            $citiesList = $region->getCities();
        }

        return $citiesList;
    }

    /**
     * Method adds new Place by the provided $placeData array if it doesn't exist yet
     *
     * @param array $placeData
     * @return Place|null
     */
    public static function addNewPlace(array $placeData)
    {
        $place = Place::findOne($placeData);

        if (!$place) {
            $place = new Place();
            $place->setAttributes($placeData);
            $place->save();
        }
        return $place;
    }

    /**
     * Method returns Place object by the specified $id
     *
     * @param $id
     * @return null|Place
     */
    public static function getPlaceById($id)
    {
        return Place::findOne($id);
    }
}
