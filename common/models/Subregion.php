<?php

namespace common\Models;

use Yii;

/**
 * This is the model class for table "subregion".
 *
 * @property string $code
 * @property string $region
 * @property string $name
 *
 * @property Region $region0
 * @property SubregionDesc[] $subregionDescs
 * @property Language[] $languages
 */
class Subregion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'subregion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['code', 'region'], 'string', 'max' => 3],
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
            'region' => 'Region',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAirports()
    {
        return $this->hasMany(Airport::className(), ['subregion' => 'code'])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCities()
    {
        return $this->hasMany(City::className(), ['subregion' => 'code'])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountries()
    {
        return $this->hasMany(Country::className(), ['subregion' => 'code'])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion0()
    {
        return $this->hasOne(Region::className(), ['code' => 'region']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubregionDescs()
    {
        return $this->hasMany(SubregionDesc::className(), ['subregion' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguages()
    {
        return $this->hasMany(Language::className(), ['code' => 'language'])->viaTable('subregion_desc', ['subregion' => 'code']);
    }
    
    public static function getSubregionByCode($subregionCode)
    {
        $subregion = Subregion::findOne([
                'code' => $subregionCode,
            ]);
        if (!$subregion) {
            $subregion = new Subregion();
            $subregion->code = $subregionCode;
            $subregion->save();
        }

        return $subregion;
    }
}
