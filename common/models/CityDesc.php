<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "city_desc".
 *
 * @property string $city
 * @property string $language
 * @property string $name
 *
 * @property City $city0
 * @property Language $language0
 */
class CityDesc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'city_desc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['city', 'language', 'name'], 'required'],
            [['city', 'language', 'name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'city' => 'City',
            'language' => 'Language',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity0()
    {
        return $this->hasOne(City::className(), ['code' => 'city']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage0()
    {
        return $this->hasOne(Language::className(), ['code' => 'language']);
    }

    /**
     * Method adds all descriptions to the specified City
     *
     * TODO:: move to City class
     *
     * @param City $city
     * @param array $cityDataArray
     */
    public static function addCityDescriptions(City $city, array $cityDataArray)
    {
        if (count($cityDataArray)>0)
        {
            foreach ($cityDataArray as $cityDataIndex => $cityDataValue) {
                CityDesc::addCityDescription($city, $cityDataIndex, $cityDataValue);
            }
        }
    }

    /**
     * Method adds particular description to the specified City using provided $cityDataIndex and $cityDataValue
     *
     * TODO:: move to City class
     *
     * @param City $city
     * @param $cityDataIndex
     * @param $cityDataValue
     * @return bool
     */
    private static function addCityDescription(City $city, $cityDataIndex, $cityDataValue)
    {
        $language = Language::getLanguageByCode($cityDataIndex);

        $cityDesc = CityDesc::findOne([
            'city' => $city->code,
            'language' => $language->code,
        ]);

        if (!$cityDesc) {
            $cityDesc = new CityDesc();
            $cityDesc->city = $city->code;
            $cityDesc->language = $language->code;
        }

        $cityDesc->name = $cityDataValue;

        return $cityDesc->save();
    }
}
