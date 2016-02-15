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
