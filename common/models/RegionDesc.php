<?php

namespace common\Models;

use Yii;

/**
 * This is the model class for table "region_desc".
 *
 * @property Region $region
 * @property Language $language
 * @property string $name
 */
class RegionDesc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'region_desc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['region', 'language', 'name'], 'required'],
            [['region'], 'string', 'max' => 3],
            [['language'], 'string', 'max' => 5],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'region' => 'Region',
            'language' => 'Language',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage0()
    {
        return $this->hasOne(Language::className(), ['code' => 'language']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['code' => 'region']);
    }

    /**
     * Method adds all Region descriptions from provided $regionDataArray to the specified $region
     *
     * TODO:: move to Region class
     *
     * @param Region $region
     * @param array $regionDataArray
     */
    public static function addRegionDescriptions(Region $region, array $regionDataArray)
    {
        if (count($regionDataArray)>0)
        {
            foreach ($regionDataArray as $regionDataIndex => $regionDataValue) {
                RegionDesc::addRegionDescription($region, $regionDataIndex, $regionDataValue);
            }
        }
    }

    /**
     * Method adds Region description from provided $regionDataIndex and $regionDataValue to the specified $region
     *
     * TODO:: move to Region class
     *
     * @param Region $region
     * @param $regionDataIndex
     * @param $regionDataValue
     * @return bool
     */
    private static function addRegionDescription(Region $region, $regionDataIndex, $regionDataValue)
    {
        $language = Language::getLanguageByCode($regionDataIndex);

        $regionDesc = RegionDesc::findOne([
            'region' => $region->code,
            'language' => $language->code,
        ]);

        if (!$regionDesc) {
            $regionDesc = new RegionDesc();
            $regionDesc->region = $region->code;
            $regionDesc->language = $language->code;
        }

        $regionDesc->name = $regionDataValue;

        return $regionDesc->save();
    }
}
