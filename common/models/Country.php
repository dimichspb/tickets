<?php

namespace common\Models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "country".
 *
 * @property string $code
 * @property string $name
 * @property string $region
 * @property string $subregion
 * @property string $currency
 *
 * @property Airport[] $airports
 * @property City[] $cities
 * @property Region $region0
 * @property Subregion $subregion0
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
    
    
    public static function getCountryByCode($countryCode)
    {
        $country = Country::findOne([
                'code' => $countryCode,
            ]);
        if (!$country) {
            $country = new Country();
            $country->code = $countryCode;
            $country->save();
        }

        return $country;
    }

    public static function getCountryByName($countryName)
    {
        $country = Country::findOne([
            'name' => $countryName,
        ]);

        return $country;
    }

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

    public static function uploadCountries($service, $dataJson)
    {
        switch ($service) {
            case 'AVS':
                Country::uploadCountriesFromAVS($dataJson);
                break;
            default:
        }
    }

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

    public static function uploadCountriesToRegions($service, $dataJson)
    {
        switch ($service) {
            case 'AVS':
                Country::uploadCountriesToRegionsFromAVS($dataJson);
                break;
            default:
        }
    }

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
