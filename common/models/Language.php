<?php

namespace common\models;

use Yii;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

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
     * @return Language
     */
    public static function getLanguageByCode($languageCode)
    {
        $language = Language::findOne([
                'code' => $languageCode,
            ]);

        return $language;
    }

    public static function getLanguageByRequestString()
    {
        $lString = Yii::$app->request->get('l')? Yii::$app->request->get('l'): Yii::$app->params['default_language'];
        return Language::getLanguageByCode($lString);
    }

    /**
     * @return array
     */
    public static function getLanguagesArray()
    {
        $result = [
            'en' => [
                'label' => 'English',
            ],
            'ru' => [
                'label' => 'Русский',
            ],
        ];
        return $result;
    }

    public static function getDefault()
    {
        return Language::get(Yii::$app->language);
    }

    public static function getDefaultCode()
    {
        return Yii::$app->language;
    }

    public static function get($languageCode = '')
    {
        if (!is_string($languageCode)) {
            var_dump($languageCode);
            die();
        }
        if ($languageCode === '') {
            $languageCode = Language::getDefaultCode();;
        }

        $languages = Language::getLanguagesArray();
        return $languages[$languageCode];
    }

    public static function label($languageCode = '')
    {
        $language = Language::get($languageCode);
        return $language['label'];
    }

    public static function defaultLabel()
    {
        return Language::label();
    }

    public static function select($languageCode = '')
    {
        if ($languageCode === '') {
            $languageCode = Language::getDefaultCode();
        }

        $languages = Language::getLanguagesArray();
        unset($languages[$languageCode]);
        return $languages;
    }

    public static function labelsList($languageCode = '')
    {
        $result = [];
        $languages = Language::select($languageCode);
        foreach ($languages as $languageCode => $languageData) {
            $result[] = [
                'label' => Html::a($languageData['label'], '/' . $languageCode),
            ];
        }
        return $result;
    }
}
