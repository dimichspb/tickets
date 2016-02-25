<?php

namespace common\Models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "subregion".
 *
 * @property string $code
 * @property string $region
 * @property string $name
 *
 * @property Airport[] $airports
 * @property City[] $cities
 * @property Region $region0
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
    
    public static function getSubregionByCode($subregionCode)
    {
        $subregion = Subregion::findOne([
                'code' => $subregionCode,
            ]);
        if (!$subregion) {
            $subregion = new Subregion();
            $subregion->code = $subregionCode;
            $subregion->save();
        }

        return $subregion;
    }

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

    public static function uploadSubregions($service, $dataJson)
    {
        switch ($service) {
            case 'AVS':
                Subregion::uploadSubregionsFromAVS($dataJson);
                break;
            default:
        }
    }

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
