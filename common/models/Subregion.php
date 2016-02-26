<?php

namespace common\Models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "subregion".
 *
 * @property string $code
 * @property Region $region
 * @property string $name
 * @property Airport[] $airports
 * @property City[] $cities
 * @property SubregionDesc[] $subregionDescs
 * @property Language[] $languages
 */
class Subregion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'subregion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['code', 'region'], 'string', 'max' => 3],
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
            'region' => 'Region',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAirports()
    {
        return $this->hasMany(Airport::className(), ['subregion' => 'code'])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCities()
    {
        return $this->hasMany(City::className(), ['subregion' => 'code'])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountries()
    {
        return $this->hasMany(Country::className(), ['subregion' => 'code'])->all();
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
    public function getSubregionDescs()
    {
        return $this->hasMany(SubregionDesc::className(), ['subregion' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguages()
    {
        return $this->hasMany(Language::className(), ['code' => 'language'])->viaTable('subregion_desc', ['subregion' => 'code']);
    }

    /**
     * Method returns Subregion object by the specified $subregionCode
     *
     * @param $subregionCode
     * @return null|static
     */
    public static function getSubregionByCode($subregionCode)
    {
        $subregion = Subregion::findOne([
                'code' => $subregionCode,
            ]);

        return $subregion;
    }

    /**
     * Method adds all Subregions to Place table
     */
    public static function addSubregionsToPlaces()
    {
        $subregions = Subregion::find()->all();

        foreach($subregions as $subregion) {
            if ($region = Region::getRegionByCode($subregion->region)) continue;
            if ($parent = Place::getPlaceByRegionCode($region->code)) continue;

            Place::addNewPlace([
                'region' => $region->code,
                'subregion' => $subregion->code,
                'parent' => $parent->id,
            ]);
        }
    }

    /**
     * Method uploads Subregions from provided JSON data depending of the specified $service code
     *
     * @param $service
     * @param $dataJson
     */
    public static function uploadSubregions($service, $dataJson)
    {
        switch ($service) {
            case 'AVS':
                Subregion::uploadSubregionsFromAVS($dataJson);
                break;
            default:
        }
    }

    /**
     * Method uploads Subregions from provided JSON data based on AVS response structure
     *
     * @param $dataJson
     */
    private static function uploadSubregionsFromAVS($dataJson)
    {
        $dataArray = Json::decode($dataJson);

        foreach ($dataArray as $item) {
            Subregion::addSubregion([
                'code' => $item['code'],
                'region' => $item['region'],
                'name' => $item['name'],
                'description' => $item['name_translations'],
            ]);
        }
    }

    /**
     * Method adds Subregion to DB
     *
     * @param array $subregionData
     * @return bool
     */
    private static function addSubregion(array $subregionData)
    {
        $subregion = Subregion::getSubregionByCode($subregionData['code']);

        if (!$subregion) {
            $subregion = new Subregion();
            $subregion->code = $subregionData['code'];
        }

        $subregion->region = $subregionData['region'];
        $subregion->name = $subregionData['name'];

        $result = $subregion->save();

        if ($result && isset($subregionData['description'])) {
            SubregionDesc::addSubregionDescriptions($subregion, $subregionData['description']);
        }

        return $result;
    }
}
