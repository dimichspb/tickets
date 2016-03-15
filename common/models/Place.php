<?php

namespace common\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;


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
    public function fields()
    {
        return [
            'id',
            'name' => function (Place $model) {
                $placeName = $model->getPlaceName();
                return $placeName? $placeName: null;
            },
        ];
    }


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
    public function getAirportDesc()
    {
        return $this->hasMany(AirportDesc::className(), ['airport' => 'code'])->via('airport');
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
    public function getCityDesc()
    {
        return $this->hasMany(CityDesc::className(), ['city' => 'code'])->via('city');
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
    public function getCountryDesc()
    {
        return $this->hasMany(CountryDesc::className(), ['country' => 'code'])->via('country');
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
    public function getRegionDesc()
    {
        return $this->hasMany(RegionDesc::className(), ['region' => 'code'])->via('region');
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
    public function getSubregionDesc()
    {
        return $this->hasMany(SubregionDesc::className(), ['subregion' => 'code'])->via('subregion');
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
        Console::stdout('Place exists! ' . serialize($placeData) . PHP_EOL);
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

    public static function getPlacesByStringDataProvider()
    {
        $qString = Yii::$app->request->get('q');

        $places = Place::getPlaceByString($qString);

        return new ActiveDataProvider([
            'query' => $places,
        ]);

    }

    /**
     * @param $qString
     * @return \yii\db\ActiveQuery
     */
    public static function getPlaceByString($qString)
    {
        if (!$qString) {
            return Place::find();
        }

        $language = Language::getLanguageByRequestString();

        $result = Place::find()
            ->leftJoin('region_desc', ['`region_desc`.`region`' => new Expression('`place`.`region`'), '`region_desc`.`language`' => $language->code])
            ->leftJoin('subregion_desc', ['`subregion_desc`.`subregion`' => new Expression('`place`.`subregion`'), '`subregion_desc`.`language`' => $language->code])
            ->leftJoin('country_desc', ['`country_desc`.`country`' => new Expression('`place`.`country`'), '`country_desc`.`language`' => $language->code])
            ->leftJoin('city_desc', ['`city_desc`.`city`' => new Expression('`place`.`city`'), '`city_desc`.`language`' => $language->code])
            ->leftJoin('airport_desc', ['`airport_desc`.`airport`' => new Expression('`place`.`airport`'), '`airport_desc`.`language`' => $language->code])

            ->andFilterWhere(['or',
                ['like', '`region_desc`.`name`', $qString],
                ['like', '`subregion_desc`.`name`', $qString],
                ['like', '`country_desc`.`name`', $qString],
                ['like', '`city_desc`.`name`', $qString],
                ['like', '`city_desc`.`city`', $qString],
                ['like', '`airport_desc`.`name`', $qString],
            ])
            ->andWhere('`place`.`airport` IS NULL')
            ->orderBy([
                '`airport_desc`.`name`' => SORT_ASC,
            ]);
        return $result;

    }

    public function getPlaceName()
    {
        $result = $this->getPlaceDescOne();

        $name = isset($result->name)? $result->name: null;
        $city = isset($result->city)? ' (' .$result->city. ')': null;

        return $name . $city;
    }

    /**
     * @param Language $language
     * @return \yii\db\ActiveQuery
     */
    private function getAirportDescByLanguage(Language $language)
    {
        $result = $this->getAirportDesc()
            ->where([
                'language' => $language->code,
            ]);

        if (!$result->count()) {
            $result = $this->getAirportDesc();
        }
        return $result;
    }

    /**
     * @param Language $language
     * @return \yii\db\ActiveQuery
     */
    private function getCityDescByLanguage(Language $language)
    {
        $result = $this->getCityDesc()
            ->where([
                'language' => $language->code,
            ]);
        if (!$result->count()) {
            $result = $this->getCityDesc();
        }
        return $result;
    }

    /**
     * @param Language $language
     * @return \yii\db\ActiveQuery
     */
    private function getCountryDescByLanguage(Language $language)
    {
        $result = $this->getCountryDesc()
            ->where([
                'language' => $language->code,
            ]);
        if (!$result->count()) {
            $result = $this->getCountryDesc();
        }
        return $result;
    }

    /**
     * @param Language $language
     * @return \yii\db\ActiveQuery
     */
    private function getSubregionDescByLanguage(Language $language)
    {
        $result = $this->getSubregionDesc()
            ->where([
                'language' => $language->code,
            ]);
        if (!$result->count()) {
            $result = $this->getSubregionDesc();
        }
        return $result;
    }

    /**
     * @param Language $language
     * @return \yii\db\ActiveQuery
     */
    private function getRegionDescByLanguage(Language $language)
    {
        $result = $this->getRegionDesc()
            ->where([
                'language' => $language->code,
            ]);
        if (!$result->count()) {
            $result = $this->getRegionDesc();
        }
        return $result;
    }

    /**
     * @param Language $language
     * @return AirportDesc
     */
    private function getAirportDescOneByLanguage(Language $language)
    {
        return $this->getAirportDescByLanguage($language)->one();
    }

    /**
     * @param Language $language
     * @return CityDesc
     */
    private function getCityDescOneByLanguage(Language $language)
    {
        return $this->getCityDescByLanguage($language)->one();
    }

    /**
     * @param Language $language
     * @return CountryDesc
     */
    private function getCountryDescOneByLanguage(Language $language)
    {
        return $this->getCountryDescByLanguage($language)->one();
    }

    /**
     * @param Language $language
     * @return SubregionDesc
     */
    private function getSubregionDescOneByLanguage(Language $language)
    {
        return $this->getSubregionDescByLanguage($language)->one();
    }

    /**
     * @param Language $language
     * @return RegionDesc
     */
    private function getRegionDescOneByLanguage(Language $language)
    {
        return $this->getRegionDescByLanguage($language)->one();
    }


    private function getPlaceDescOne()
    {
        return $this->getPlaceDesc()->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlaceDesc()
    {
        $language = Language::getLanguageByRequestString();

        if (isset($this->airport)) return $this->getAirportDescByLanguage($language);
        if (isset($this->city)) return $this->getCityDescByLanguage($language);
        if (isset($this->country)) return $this->getCountryDescByLanguage($language);
        if (isset($this->subregion)) return $this->getSubregionDescByLanguage($language);
        return $this->getRegionDescByLanguage($language);

    }
}
