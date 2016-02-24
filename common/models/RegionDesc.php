<?php

namespace common\Models;

use Yii;

/**
 * This is the model class for table "region_desc".
 *
 * @property string $region
 * @property string $language
 * @property string $name
 *
 * @property Language $language0
 * @property Region $region0
 */
class RegionDesc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'region_desc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['region', 'language', 'name'], 'required'],
            [['region'], 'string', 'max' => 3],
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
            'region' => 'Region',
            'language' => 'Language',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage0()
    {
        return $this->hasOne(Language::className(), ['code' => 'language']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion0()
    {
        return $this->hasOne(Region::className(), ['code' => 'region']);
    }
}