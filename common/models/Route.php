<?php

namespace common\Models;

use Yii;

/**
 * This is the model class for table "route".
 *
 * @property integer $id
 * @property string $origin_airport
 * @property string $destination_airport
 * @property string $there_date
 * @property string $back_date
 *
 * @property Airport $destinationAirport
 * @property Airport $originAirport
 */
class Route extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'route';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin_airport', 'destination_airport', 'there_date'], 'required'],
            [['there_date', 'back_date'], 'safe'],
            [['origin_airport', 'destination_airport'], 'string', 'max' => 3]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'origin_airport' => 'Origin Airport',
            'destination_airport' => 'Destination Airport',
            'there_date' => 'There Date',
            'back_date' => 'Back Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDestinationAirport()
    {
        return $this->hasOne(Airport::className(), ['code' => 'destination_airport']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOriginAirport()
    {
        return $this->hasOne(Airport::className(), ['code' => 'origin_airport']);
    }
}
