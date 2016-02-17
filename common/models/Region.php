<?php

namespace common\Models;

use Yii;

/**
 * This is the model class for table "region".
 *
 * @property string $code
 * @property string $name
 */
class Region extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'region';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['code'], 'string', 'max' => 3],
            [['name'], 'string', 'max' => 255]
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAirports()
    {
        return $this->hasMany(Airport::className(), ['region' => 'code'])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCities()
    {
        return $this->hasMany(City::className(), ['region' => 'code'])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountries()
    {
        return $this->hasMany(Country::className(), ['region' => 'code'])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubregions()
    {
        return $this->hasMany(Subregion::className(), ['region' => 'code'])->all();
    }

    public static function getRegionByCode($regionCode)
    {
        $region = Region::findOne([
                'code' => $regionCode,
            ]);
        if (!$region) {
            $region = new Region();
            $region->code = $regionCode;
            $region->save();
        }

        return $region;
    }
}
