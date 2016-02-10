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

    public static function getLanguageByCode($languageCode)
    {
        $language = Language::find()
            ->where([
                'code' => $languageCode,
            ])->one();
        if (!$language) {
            $language = new Language();
            $language->code = $languageCode;
            $language->save();
        }

        return $language;
    }
}
