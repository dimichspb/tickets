<?php

namespace common\models;

use Yii;
use yii\helpers\Json;

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

    /**
     * Method returns Airport object by specified $airportCode
     *
     * @param $airportCode
     * @return Airport|null
     */
    public static function getAirportByCode($airportCode)
    {
        $airport = Airport::findOne([
            'code' => $airportCode,
        ]);

        return $airport;
    }

    /**
     * Method returns City object of the Airport specified by $airportCode
     *
     * @param $airportCode
     * @return City|null
     */
    public static function getCityByCode($airportCode)
    {
        $airport = Airport::findOne([
            'code' => $airportCode,
        ]);

        if ($airport) {
            return $airport->getCity()->one();
        }
    }

    /**
     * Method adds existing Airports to Place table
     *
     */
    public static function addAirportsToPlaces()
    {
        $airports = Airport::find()->all();

        foreach ($airports as $airport) {
            if ($city = City::getCityByCode($airport->city)) continue;
            if ($country = Country::getCountryByCode($city->country)) continue;
            if ($subregion = Subregion::getSubregionByCode($country->subregion)) continue;
            if ($region = Region::getRegionByCode($subregion->region)) continue;
            if ($parent = Place::getPlaceByCityCode($city->code)) continue;

            Place::addNewPlace([
                'region' => $region->code,
                'subregion' => $subregion->code,
                'country' => $country->code,
                'city' => $city->code,
                'airport' => $airport->code,
                'parent' => $parent->id,
            ]);
        }
    }

    /**
     * Method uploads Airports from provided JSON data by specified $service code
     *
     * @param $service
     * @param $dataJson
     */
    public static function uploadAirports($service, $dataJson)
    {
        switch ($service) {
            case 'AVS':
                Airport::uploadAirportsFromAVS($dataJson);
                break;
            default:
        }
    }

    /**
     * Method uploads Airports data from provided JSON data based on structure of AVS response
     *
     * @param $dataJson
     */
    private static function uploadAirportsFromAVS($dataJson)
    {
        $dataArray = Json::decode($dataJson);

        foreach ($dataArray as $item) {
            Airport::addAirport([
                'code' => $item['code'],
                'name' => $item['name'],
                'coordinates' => serialize($item['coordinates']),
                'description' => $item['name_translations'],
                'time_zone' => $item['time_zone'],
                'country' => $item['country_code'],
                'city' => $item['city_code'],
            ]);
        }
    }

    /**
     * Method adds new Airport to DB
     *
     * @param $airportData
     */
    private static function addAirport($airportData)
    {
        $country = Country::getCountryByCode($airportData['country']);
        $city = City::getCityByCode($airportData['city']);

        $airport = Airport::getAirportByCode($airportData['code']);

        if (!$airport) {
            $airport = new Airport();
            $airport->code = $airportData['code'];
        }

        $airport->name = $airportData['name'];
        $airport->time_zone = $airportData['time_zone'];
        $airport->coordinates = $airportData['coordinates'];
        $airport->country = $country->code;
        $airport->city = $city->code;

        $result = $airport->save();

        if ($result && isset($airportData['description'])) {
            AirportDesc::addAirportDescriptions($airport, $airportData['description']);
        }
    }

    /**
     * Method sets Airports region and subregion attributes based on provided $countryData
     *
     * @param $countryData
     */
    public static function updateAirportsRegionsByCountry($countryData)
    {
        $airports = Airport::findAll([
            'country' => $countryData['country'],
        ]);

        foreach ($airports as $airport) {
            $airport->region = $countryData['region'];
            $airport->subregion = $countryData['subregion'];
            $airport->save();
        }
    }
}
