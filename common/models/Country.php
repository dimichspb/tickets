<?php

namespace common\Models;

use Yii;

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
}
