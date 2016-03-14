<?php

namespace common\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "country".
 *
 * @property string $code
 * @property string $name
 * @property Region $region
 * @property Subregion $subregion
 * @property Currency $currency
 * @property Airport[] $airports
 * @property City[] $cities
 * @property CountryDesc[] $countryDescs
 * @property Language[] $languages
 * @property Place[] $places
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['code'], 'string', 'max' => 2],
            [['name'], 'string', 'max' => 255],
            [['region', 'subregion', 'currency'], 'string', 'max' => 3],
            [['code'], 'unique']
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
            'region' => 'Region',
            'subregion' => 'Subregion',
            'currency' => 'Currency',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAirports()
    {
        return $this->hasMany(Airport::className(), ['country' => 'code'])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCities()
    {
        return $this->hasMany(City::className(), ['country' => 'code'])->all();
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
     * @return \yii\db\ActiveQuery
     */
    public function getCountryDescs()
    {
        return $this->hasMany(CountryDesc::className(), ['country' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguages()
    {
        return $this->hasMany(Language::className(), ['code' => 'language'])->viaTable('country_desc', ['country' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlaces()
    {
        return $this->hasMany(Place::className(), ['country' => 'code']);
    }

    /**
     * Method returns Country object by specified $countryCode
     *
     * @param $countryCode
     * @return Country|null
     */
    public static function getCountryByCode($countryCode)
    {
        $country = Country::findOne([
                'code' => $countryCode,
            ]);

        return $country;
    }

    /**
     * Method returns Country object by specified $countryName
     *
     * @param $countryName
     * @return null|static
     */
    public static function getCountryByName($countryName)
    {
        $country = Country::findOne([
            'name' => $countryName,
        ]);

        return $country;
    }

    /**
     * Method adds all Countries to Place table
     *
     */
    public static function addCountriesToPlaces()
    {
        $countries = Country::find()->all();

        foreach($countries as $country) {
            if ($subregion = Subregion::getSubregionByCode($country->subregion)) continue;
            if ($region = Region::getRegionByCode($subregion->region)) continue;
            if ($parent = Place::getPlaceBySubregionCode($subregion->code)) continue;

            Place::addNewPlace([
                'region' => $region->code,
                'subregion' => $subregion->code,
                'country' => $country->code,
                'parent' => $parent->id,
            ]);
        }
    }

    /**
     * Method uploads Countries from provided JSON data depending on specified $service code
     *
     * @param $service
     * @param $dataJson
     */
    public static function uploadCountries($service, $dataJson)
    {
        switch ($service) {
            case 'AVS':
                Country::uploadCountriesFromAVS($dataJson);
                break;
            default:
        }
    }

    /**
     * Method uploads Countries from provided JSON data based on AVS response structure
     *
     * @param $dataJson
     */
    private static function uploadCountriesFromAVS($dataJson)
    {
        $dataArray = Json::decode($dataJson);

        foreach ($dataArray as $item) {
            Country::addCountry([
                'code' => $item['code'],
                'name' => $item['name'],
                'currency' => $item['currency'],
                'description' => $item['name_translations'],
            ]);
        }
    }

    /**
     * Method adds Country to DB
     *
     * @param $countryData
     * @return bool
     */
    private static function addCountry($countryData)
    {
        $country = Country::getCountryByCode($countryData['code']);

        if (!$country) {
            $country = new Country();
            $country->code = $countryData['code'];
        }

        $country->name = $countryData['name'];
        $country->currency = !empty($countryData['currency'])? $countryData['currency']: NULL;

        $result = $country->save();

        if ($result && isset($countryData['description'])) {
            CountryDesc::addCountryDescriptions($country, $countryData['description']);
        }

        return $result;
    }

    /**
     * Method uploads Country-to-region relations from provided JSON data depending on specified $service code
     *
     * @param $service
     * @param $dataJson
     */
    public static function uploadCountriesToRegions($service, $dataJson)
    {
        switch ($service) {
            case 'AVS':
                Country::uploadCountriesToRegionsFromAVS($dataJson);
                break;
            default:
        }
    }

    /**
     * Method uploads Country-to-region relations from provided JSON data based on AVS response structure, and changes
     * city's and airport's region and subregion attributes as well
     *
     * @param $dataJson
     */
    private static function uploadCountriesToRegionsFromAVS($dataJson)
    {
        $dataArray = Json::decode($dataJson);

        foreach ($dataArray as $item) {
            Country::updateCountryRegion([
                'code' => $item['country'],
                'region' => $item['region'],
                'subregion' => $item['subregion'],
            ]);

            City::updateCitiesRegionsByCountry([
                'country' => $item['country'],
                'region' => $item['region'],
                'subregion' => $item['subregion'],
            ]);

            Airport::updateAirportsRegionsByCountry([
                'country' => $item['country'],
                'region' => $item['region'],
                'subregion' => $item['subregion'],
            ]);
        }
    }

    /**
     * Method updates Region and Subregion attributes at the Country specified by $countryData's 'code' element
     *
     * @param $countryData
     * @return bool
     */
    private static function updateCountryRegion($countryData)
    {
        $country = Country::getCountryByCode($countryData['code']);

        if (!$country) {
            $country = new Country();
            $country->code = $countryData['code'];
        }

        $country->region = $countryData['region'];
        $country->subregion = $countryData['subregion'];

        $result = $country->save();

        return $result;
    }
}
