<?php

namespace common\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "city".
 *
 * @property string $code
 * @property string $name
 * @property string $coordinates
 * @property string $time_zone
 * @property Region $region
 * @property Subregion $subregion
 * @property Country $country
 * @property Airport[] $airports
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
        return $this->hasMany(Airport::className(), ['city' => 'code'])->all();
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

    /**
     * Method returns City object by a specified $cityCode
     *
     * @param $cityCode
     * @return City|null|static
     */
    public static function getCityByCode($cityCode)
    {
        $city = City::findOne([
                'code' => $cityCode,
            ]);

        return $city;
    }

    /**
     * Method adds all Cities to Place table
     *
     */
    public static function addCitiesToPlaces()
    {
        $cities = City::find()->all();

        foreach($cities as $city) {
            var_dump($city->code);
            if (!$country = Country::getCountryByCode($city->country)) continue;
            if (!$subregion = Subregion::getSubregionByCode($country->subregion)) continue;
            if (!$region = Region::getRegionByCode($subregion->region)) continue;
            if (!$parent = Place::getPlaceByCountryCode($country->code)) continue;

            Place::addNewPlace([
                'region' => $region->code,
                'subregion' => $subregion->code,
                'country' => $country->code,
                'city' => $city->code,
                'parent' => $parent->id,
            ]);
        }
    }

    /**
     * Method uploads Cities from provided JSON data depends on specified $service code
     *
     * @param $service
     * @param $dataJson
     */
    public static function uploadCities($service, $dataJson)
    {
        switch ($service) {
            case 'AVS':
                City::uploadCitiesFromAVS($dataJson);
                break;
            default:
        }
    }

    /**
     * Method uploads Cities from provided JSON data based on AVS response structure
     *
     * @param $dataJson
     */
    private static function uploadCitiesFromAVS($dataJson)
    {
        $dataArray = Json::decode($dataJson);

        foreach ($dataArray as $item) {
            City::addCity([
                'code' => $item['code'],
                'name' => $item['name'],
                'coordinates' => serialize($item['coordinates']),
                'description' => $item['name_translations'],
                'time_zone' => $item['time_zone'],
                'country' => $item['country_code'],
            ]);
        }
    }

    /**
     * Method adds City to DB
     *
     * @param $cityData
     * @return bool
     */
    private static function addCity($cityData)
    {
        $country = Country::getCountryByCode($cityData['country']);

        $city = City::getCityByCode($cityData['code']);

        if (!$city) {
            $city = new City();
            $city->code = $cityData['code'];
        }

        $city->name = $cityData['name'];
        $city->coordinates = $cityData['coordinates'];
        $city->time_zone = $cityData['time_zone'];
        $city->country = $country->code;

        $result = $city->save();

        if ($result && isset($cityData['description'])) {
            CityDesc::addCityDescriptions($city, $cityData['description']);
        }

        return $result;
    }

    /**
     * Method updates City's region and subregion attributes based on provided $countryData
     *
     * @param $countryData
     */
    public static function updateCitiesRegionsByCountry($countryData)
    {
        $cities = City::findAll([
            'country' => $countryData['country'],
        ]);

        foreach ($cities as $city) {
            $city->region = $countryData['region'];
            $city->subregion = $countryData['subregion'];
            $city->save();
        }
    }

    /**
     * @param $languageCode
     * @return CityDesc
     */
    public function getCityDescByLanguage(Language $language)
    {
        return $this->getCityDescs()->where([
            'language' => $language->code,
        ])->one();
    }

}
