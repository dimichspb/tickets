<?php

namespace common\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "region".
 *
 * @property string $code
 * @property string $name
 *
 * @property Airport[] $airports
 * @property City[] $cities
 */
class Region extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'region';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['code'], 'string', 'max' => 3],
            [['name'], 'string', 'max' => 255]
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAirports()
    {
        return $this->hasMany(Airport::className(), ['region' => 'code'])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCities()
    {
        return $this->hasMany(City::className(), ['region' => 'code'])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountries()
    {
        return $this->hasMany(Country::className(), ['region' => 'code'])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubregions()
    {
        return $this->hasMany(Subregion::className(), ['region' => 'code'])->all();
    }

    /**
     * Method returns Region object by the specified $regionCode
     *
     * @param $regionCode
     * @return Region|null
     */
    public static function getRegionByCode($regionCode)
    {
        $region = Region::findOne([
                'code' => $regionCode,
            ]);

        return $region;
    }

    /**
     * Method adds all Regions to Place table
     *
     */
    public static function addRegionsToPlaces()
    {
        $regions = Region::find()->all();

        foreach($regions as $region) {

            Place::addNewPlace([
                'region' => $region->code,
            ]);
        }
    }

    /**
     * Method uploads Regions from provided JSON data depending on specified $service code
     *
     * @param $service
     * @param $dataJson
     */
    public static function uploadRegions($service, $dataJson)
    {
        switch ($service) {
            case 'AVS':
                Region::uploadRegionsFromAVS($dataJson);
                break;
            default:
        }
    }

    /**
     * Method upload Regions from provided JSON data based on AVS response structure
     *
     * @param $dataJson
     */
    private static function uploadRegionsFromAVS($dataJson)
    {
        $dataArray = Json::decode($dataJson);

        foreach ($dataArray as $item) {
            Region::addRegion([
                'code' => $item['code'],
                'name' => $item['name'],
                'description' => $item['name_translations'],
            ]);
        }
    }

    /**
     * Method adds Region to DB
     *
     * @param $regionData
     * @return bool
     */
    private static function addRegion($regionData)
    {
        $region = Region::getRegionByCode($regionData['code']);

        if (!$region) {
            $region = new Region();
            $region->code = $regionData['code'];
        }

        $region->name = $regionData['name'];

        $result = $region->save();

        if ($result && isset($regionData['description'])) {
            RegionDesc::addRegionDescriptions($region, $regionData['description']);
        }

        return $result;
    }
}
