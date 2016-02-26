<?php

namespace common\Models;

use Yii;

/**
 * This is the model class for table "language".
 *
 * @property string $code
 *
 * @property CountryDesc[] $countryDescs
 */
class Language extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'language';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['code'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'code' => 'Code',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountryDescs()
    {
        return $this->hasMany(CountryDesc::className(), ['language' => 'code']);
    }

    /**
     * Method returns Language object by the specified $languageCode
     *
     * @param $languageCode
     * @return null|static
     */
    public static function getLanguageByCode($languageCode)
    {
        $language = Language::findOne([
                'code' => $languageCode,
            ]);

        return $language;
    }
}
