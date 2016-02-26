<?php

namespace common\Models;

use Yii;

/**
 * This is the model class for table "country_desc".
 *
 * @property Country $country
 * @property Language $language
 * @property string $name
 */
class CountryDesc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country_desc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country', 'language', 'name'], 'required'],
            [['country', 'language', 'name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'country' => 'Country',
            'language' => 'Language',
            'name' => 'Name',
        ];
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
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['code' => 'language']);
    }

    /**
     * Method adds all descriptions from the provided $countryDataArray to the specified $country
     *
     * TODO:: move to Country class
     *
     * @param Country $country
     * @param array $countryDataArray
     */
    public static function addCountryDescriptions(Country $country, array $countryDataArray)
    {
        if (count($countryDataArray)>0)
        {
            foreach ($countryDataArray as $countryDataIndex => $countryDataValue) {
                CountryDesc::addCountryDescription($country, $countryDataIndex, $countryDataValue);
            }
        }
    }

    /**
     * Method adds particular description to the specified $country using provided $countryDataIndex and $countryDataValue
     *
     * TODO:: move to Country class
     *
     * @param Country $country
     * @param $countryDataIndex
     * @param $countryDataValue
     * @return bool
     */
    private static function addCountryDescription(Country $country, $countryDataIndex, $countryDataValue)
    {
        $language = Language::getLanguageByCode($countryDataIndex);

        $countryDesc = CountryDesc::findOne([
            'country' => $country->code,
            'language' => $language->code,
        ]);

        if (!$countryDesc) {
            $countryDesc = new CountryDesc();
            $countryDesc->country = $country->code;
            $countryDesc->language = $language->code;
        }

        $countryDesc->name = $countryDataValue;

        return $countryDesc->save();
    }
}
