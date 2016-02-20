<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "airline".
 *
 * @property integer $id
 * @property string $name
 * @property string $alias
 * @property string $iata
 * @property string $icao
 * @property string $callsign
 * @property string $country
 * @property integer $is_active
 *
 * @property Country $country0
 * @property Rate[] $rates
 */
class Airline extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'airline';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['is_active'], 'integer'],
            [['name', 'alias', 'callsign'], 'string', 'max' => 255],
            [['iata', 'country'], 'string', 'max' => 2],
            [['icao'], 'string', 'max' => 3]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'alias' => 'Alias',
            'iata' => 'Iata',
            'icao' => 'Icao',
            'callsign' => 'Callsign',
            'country' => 'Country',
            'is_active' => 'Is Active',
        ];
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
    public function getRates()
    {
        return $this->hasMany(Rate::className(), ['airline' => 'id']);
    }

    public static function getAirlineByName($airlineName)
    {
        $airline = Airline::findOne([
            'name' => $airlineName,
        ]);
        if (!$airline) {
            $airline = new Airline();
            $airline->name = $airlineName;
            $airline->save();
        }

        return $airline;
    }
}
