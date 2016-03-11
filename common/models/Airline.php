<?php

namespace common\Models;

use Yii;
use yii\helpers\Json;

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

    /**
     * Method returns Airline object by specified $airlineName
     *
     * @param $airlineName
     * @return Airline|null
     */
    public static function getAirlineByName($airlineName)
    {
        $airline = Airline::findOne([
            'name' => $airlineName,
        ]);

        return $airline;
    }

    /**
     * Method uploads Airlines data from provided JSON data file depends on specified $service code
     *
     * @param $service
     * @param $dataJson
     */
    public static function uploadAirlines($service, $dataJson)
    {
        switch ($service) {
            case 'AVS':
                Airline::uploadAirlinesFromAVS($dataJson);
                break;
            default:
        }
    }

    /**
     * Method uploads Airlines data from provided JSON data based on AVS response structure
     *
     * @param $dataJson
     */
    private static function uploadAirlinesFromAVS($dataJson)
    {
        $dataArray = Json::decode($dataJson);

        foreach ($dataArray as $item) {
            Airline::addAirline([
                'name' => $item['name'],
                'alias' => $item['alias'],
                'iata' => $item['iata'],
                'icao' => $item['icao'],
                'callsign' => $item['callsign'],
                'country' => $item['country'],
                'is_active' => $item['is_active'],
            ]);
        }
    }

    /**
     * Method adds Airline data to DB
     *
     * @param $airlineData
     * @return bool
     */
    private static function addAirline($airlineData)
    {
        $airline = Airline::getAirlineByName($airlineData['name']);

        if (!$airline) {
            $airline = new Airline();
            $airline->name = $airlineData['name'];
        }

        $airline->alias = $airlineData['alias'];
        $airline->iata = $airlineData['iata'];
        $airline->icao = $airlineData['icao'];
        $airline->callsign = $airlineData['callsign'];
        $airline->country = Country::getCountryByName($airlineData['country'])? Country::getCountryByName($airlineData['country'])->code: NULL;
        $airline->is_active = $airlineData['is_active'];

        $result = $airline->save();

        return $result;
    }

}
