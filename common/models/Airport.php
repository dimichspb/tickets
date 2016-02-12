<?php

namespace common\Models;

use Yii;

/**
 * This is the model class for table "airport".
 *
 * @property string $code
 * @property string $name
 * @property string $coordinates
 * @property string $time_zone
 * @property string $country
 * @property string $city
 *
 * @property City $city0
 * @property Country $country0
 * @property AirportDesc[] $airportDescs
 * @property Language[] $languages
 */
class Airport extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'airport';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'country', 'city'], 'required'],
            [['code', 'city'], 'string', 'max' => 3],
            [['name', 'coordinates', 'time_zone'], 'string', 'max' => 255],
            [['country'], 'string', 'max' => 2]
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
            'coordinates' => 'Coordinates',
            'time_zone' => 'Time Zone',
            'country' => 'Country',
            'city' => 'City',
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
    public function getCountry0()
    {
        return $this->hasOne(Country::className(), ['code' => 'country']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAirportDescs()
    {
        return $this->hasMany(AirportDesc::className(), ['airport' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguages()
    {
        return $this->hasMany(Language::className(), ['code' => 'language'])->viaTable('airport_desc', ['airport' => 'code']);
    }

    public static function getAirportByCode($airportCode)
    {
        $airport = Airport::findOne([
            'code' => $airportCode,
        ]);
        if (!$airport) {
            $airport = new Airport();
            $airport->code = $airportCode;
            $airport->save();
        }

        return $airport;
    }
}
