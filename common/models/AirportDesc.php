<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "airport_desc".
 *
 * @property Airport $airport
 * @property Language $language
 * @property string $name
 *
 */
class AirportDesc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'airport_desc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['airport', 'language'], 'required'],
            [['airport'], 'string', 'max' => 3],
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
            'airport' => 'Airport',
            'language' => 'Language',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAirport()
    {
        return $this->hasOne(Airport::className(), ['code' => 'airport']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['code' => 'language']);
    }

    /**
     * Method adds all descriptions from provided $airportDataArray to the specified $airport
     *
     * TODO:: move to Airport class
     *
     * @param Airport $airport
     * @param array $airportDataArray
     */
    public static function addAirportDescriptions(Airport $airport, array $airportDataArray)
    {
        if (count($airportDataArray)>0)
        {
            foreach ($airportDataArray as $airportDataIndex => $airportDataValue) {
                AirportDesc::addAirportDescription($airport, $airportDataIndex, $airportDataValue);
            }
        }
    }

    /**
     * Method adds particular description from provided $airportDataIndex and $airportDataValue to the
     * specified $airport
     *
     * TODO:: move to Airport class
     *
     * @param Airport $airport
     * @param $airportDataIndex
     * @param $airportDataValue
     * @return bool
     */
    private static function addAirportDescription(Airport $airport, $airportDataIndex, $airportDataValue)
    {
        $language = Language::getLanguageByCode($airportDataIndex);

        $airportDesc = AirportDesc::findOne([
            'airport' => $airport->code,
            'language' => $language->code,
        ]);

        if (!$airportDesc) {
            $airportDesc = new AirportDesc();
            $airportDesc->airport = $airport->code;
            $airportDesc->language = $language->code;
        }

        $airportDesc->name = $airportDataValue;

        return $airportDesc->save();
    }
}
