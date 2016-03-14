<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "subregion_desc".
 *
 * @property Subregion $subregion
 * @property Language $language
 * @property string $name
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
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['code' => 'language']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubregion()
    {
        return $this->hasOne(Subregion::className(), ['code' => 'subregion']);
    }

    /**
     * Method adds all Subregion descriptions from provided $subregionDataArray to the specified $subregion
     *
     * TODO:: move to Subregion class
     *
     * @param Subregion $subregion
     * @param array $subregionDataArray
     */
    public static function addSubregionDescriptions(Subregion $subregion, array $subregionDataArray)
    {
        if (count($subregionDataArray)>0)
        {
            foreach ($subregionDataArray as $subregionDataIndex => $subregionDataValue) {
                SubregionDesc::addSubregionDescription($subregion, $subregionDataIndex, $subregionDataValue);
            }
        }
    }

    /**
     * Method adds description to the specified Subregion based on specified $subregionDataIndex and $subregionDataValue
     *
     * TODO:: move to Subregion class
     *
     * @param Subregion $subregion
     * @param $subregionDataIndex
     * @param $subregionDataValue
     * @return bool
     */
    private static function addSubregionDescription(Subregion $subregion, $subregionDataIndex, $subregionDataValue)
    {
        $language = Language::getLanguageByCode($subregionDataIndex);

        $subregionDesc = SubregionDesc::findOne([
            'subregion' => $subregion->code,
            'language' => $language->code,
        ]);

        if (!$subregionDesc) {
            $subregionDesc = new SubregionDesc();
            $subregionDesc->subregion = $subregion->code;
            $subregionDesc->language = $language->code;
        }

        $subregionDesc->name = $subregionDataValue;

        return $subregionDesc->save();
    }
}
