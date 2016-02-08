<?php

namespace common\Models;

use Yii;

/**
 * This is the model class for table "country".
 *
 * @property string $code
 * @property string $name
 * @property string $currency
 *
 * @property CountryDesc[] $countryDescs
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
            [['code', 'name', 'currency'], 'required'],
            [['code', 'name', 'currency'], 'string', 'max' => 255],
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
            'currency' => 'Currency',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountryDescs()
    {
        return $this->hasMany(CountryDesc::className(), ['country' => 'code']);
    }
}
