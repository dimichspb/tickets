<?php

namespace common\Models;

use Yii;

/**
 * This is the model class for table "airport_desc".
 *
 * @property string $airport
 * @property string $language
 * @property string $name
 *
 * @property Airport $airport0
 * @property Language $language0
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
    public function getAirport0()
    {
        return $this->hasOne(Airport::className(), ['code' => 'airport']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage0()
    {
        return $this->hasOne(Language::className(), ['code' => 'language']);
    }
}
