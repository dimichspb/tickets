<?php

namespace common\Models;

use Yii;

/**
 * This is the model class for table "subregion_desc".
 *
 * @property string $subregion
 * @property string $language
 * @property string $name
 *
 * @property Language $language0
 * @property Subregion $subregion0
 */
class SubregionDesc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'subregion_desc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subregion', 'language', 'name'], 'required'],
            [['subregion'], 'string', 'max' => 3],
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
            'subregion' => 'Subregion',
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
    public function getSubregion0()
    {
        return $this->hasOne(Subregion::className(), ['code' => 'subregion']);
    }
}
