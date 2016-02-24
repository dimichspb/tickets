<?php

namespace common\Models;

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
}