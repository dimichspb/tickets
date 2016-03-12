<?php

namespace common\models;

use Yii;
use common\models\Airport;
use common\models\Place;
use common\models\Route;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "request".
 *
 * @property integer $id
 * @property string $create_date

 * @property string $there_start_date
 * @property string $there_end_date
 * @property string $travel_period_start
 * @property string $travel_period_end
 * @property integer $status
 * @property integer $mailing_processed
 *
 * @property Place $destination
 * @property Place $origin
 * @property User $user
 */
class Request extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 0;
    const STATUS_INACTIVE = 1;
    const STATUS_DELETED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'request';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_date', 'there_start_date', 'there_end_date'], 'safe'],
            [['user', 'origin', 'destination', 'there_start_date', 'there_end_date'], 'required'],
            [['user', 'origin', 'destination', 'status', 'travel_period_start', 'travel_period_end'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'create_date' => 'Create Date',
            'user' => 'User',
            'origin' => 'Origin',
            'destination' => 'Destination',
            'there_start_date' => 'There Start Date',
            'there_end_date' => 'There End Date',
            'travel_period_start' => 'Travel period start',
            'travel_period_end' => 'Travel period end',
            'status' => 'Status',
            'mailing_processed' => 'Processed for mailing',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDestination()
    {
        return $this->hasOne(Place::className(), ['id' => 'destination']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrigin()
    {
        return $this->hasOne(Place::className(), ['id' => 'origin']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoutes()
    {
        return $this->hasMany(Route::className(), ['id' => 'route'])->viaTable('request_to_route', ['request' => 'id']);
    }

    /**
     * @return Route[]
     */
    public function getRoutesArray()
    {
        return $this->getRoutes()->all();
    }
    /**
     * @return array|Request[]
     */
    public static function getAllRequests()
    {
        return Request::find()->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getAllActiveRequests()
    {
        return Request::find()
            ->where([
                'status' => self::STATUS_ACTIVE,
            ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getRequestsToMail()
    {
        return Request::getAllActiveRequests()
            ->andWhere([
                'email' => 1,
            ]);
    }

    /**
     * @return Request[]
     */
    public static function getRequestsToMailArray()
    {
        return Request::getRequestsToMail()->all();
    }

    /**
     * Method returns Request object by the specified $requestId
     *
     * @param $requestId
     * @return Request|null
     */
    public static function getRequestById($requestId)
    {
        return Request::findOne($requestId);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRates()
    {
        return $this->hasMany(Rate::className(), ['route' => 'id'])->via('routes')->orderBy('price');
    }

    /*
     * return integer;
     */
    public function isMailingProcessed()
    {
        return $this->mailing_processed;
    }

    /**
     * @return User
     */
    public function getUserOne()
    {
        return $this->getUser()->one();
    }

    /**
     * @return Language
     */
    public function getUserLanguage()
    {
        return $this->getUserOne()->getLanguageOne();
    }
/*
    public function getRatesByCreateDate(\DateTime $dateTime)
    {
        return $this
            ->getRates()
            ->where([
                '>=', 'create_date', $dateTime->format('Y-m-d 00:00:00')
            ])
            ->andWhere([
                '<',  'create_date', $dateTime->add(new \DateInterval('P1D'))->format('Y-m-d 00:00:00')
            ]);
    }
*/
}
