<?php

namespace common\models;

use Yii;
use common\models\Request;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

/**
 * This is the model class for table "route".
 *
 * @property integer $id
 * @property string $origin_city
 * @property string $destination_city
 * @property string $there_date
 * @property string $back_date
 * @property string $currency
 * @property integer $status
 * @property City $destinationCity
 * @property City $originCity
 */
class Route extends \yii\db\ActiveRecord
{
    private static $limit;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'route';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin_city', 'destination_city', 'there_date'], 'required'],
            [['there_date', 'back_date'], 'safe'],
            [['origin_city', 'destination_city', 'currency'], 'string', 'max' => 3]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'origin_city' => 'Origin City',
            'destination_city' => 'Destination City',
            'there_date' => 'There Date',
            'back_date' => 'Back Date',
            'currency' => 'Currency',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDestinationCity()
    {
        return $this->hasOne(City::className(), ['code' => 'destination_city']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOriginCity()
    {
        return $this->hasOne(City::className(), ['code' => 'origin_city']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency()
    {
        return $this->hasOne(Currency::className(), ['code' => 'currency']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequests()
    {
        return $this->hasMany(Request::className(), ['id' => 'request'])->viaTable('request_to_route', ['route' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRates()
    {
        return $this->hasMany(Rate::className(), ['route' => 'id']);
    }

    /**
     * Method avoids saving similar Routes
     *
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $route = Route::findOne([
                'origin_city' => $this->origin_city,
                'destination_city' => $this->destination_city,
                'there_date' => $this->there_date,
                'back_date' => $this->back_date,
                'currency' => $this->currency,
            ]);
            if ($route) {
                return;
            }
        }
        return parent::beforeSave($insert);
    }

    /**
     * Method returns Routes with not up-to-date Rate
     *
     * @param integer $limit
     * @return array
     */
    public static function getRoutesWithOldRate($limit = 100)
    {
        $query = Route::find()
            ->select('`route`.*, count(`rate`.`id`) as `rates`')
            ->leftJoin('rate', '`rate`.`route`=`route`.`id` AND DATE(`rate`.`create_date`) = CURDATE()')
            ->groupBy('`route`.`id`')
            ->limit($limit)
            ->having('`rates` = 0');

        $result = $query->where(['route.status' => '0'])->all();

        if (count($result) > 0) {
            return($result);
        }
    }

    /**
     * Method return Routes by the specified $requestId and with not up-to-date Rate
     *
     * @param integer $requestId
     * @param integer $limit
     * @return array
     */
    public static function getRoutesWithOldRateByRequestId($requestId, $limit = 100)
    {
        $query = Route::find()
            ->select('`route`.*, `request_to_route`.*, count(`rate`.`id`) as `rates`')
            ->innerJoin('request_to_route', '`request_to_route`.`route` = `route`.`id` AND `request_to_route`.`request`=' . $requestId)
            ->leftJoin('rate', '`rate`.`route`=`route`.`id` AND DATE(`rate`.`create_date`) = CURDATE()')
            ->groupBy('`route`.`id`')
            ->limit($limit)
            ->having('`rates` = 0');

        $result = $query->where(['route.status' => '0'])->all();

        if (count($result) > 0) {
            return($result);
        }
    }

    /**
     * Method sets the limit of requests to providers
     *
     * @param $limit
     */
    private static function setLimit($limit)
    {
        self::$limit = $limit;
    }

    /**
     * Method checks whether limit is exceed
     */
    private static function checkLimit()
    {
        if (isset(self::$limit)) {
            if (--self::$limit <= 0) exit();
        }
    }

    /**
     * @param \DateTime $dateTime
     * @return \yii\db\ActiveQuery
     */
    public function getRatesByCreateDate(\DateTime $dateTime)
    {
        $nextDay = clone $dateTime;
        $nextDay->add(new \DateInterval('P1D'));

        $minRates = $this->getRates()->select('id, min(price)')->groupBy('YEAR(create_date), MONTH(create_date), DAY(create_date)');

        $result = $this
            ->getRates()
            ->innerJoin([
                'r' => $minRates
            ], 'r.id = rate.id')
            ->where([
                '<=', 'rate.create_date', $nextDay->format('Y-m-d 00:00:00')
            ])
            ->orderBy([
                'rate.create_date' => SORT_DESC,
            ]);

        return $result;

    }

    /**
     * @param \DateTime $dateTime
     * @return Rate;
     */
    private function getBestRateByCreateDate(\DateTime $dateTime)
    {
        return $this->getRatesByCreateDate($dateTime)->one();
    }

    public function getBetterRate(\DateTime $dateTime, $returnEquals = false)
    {
        $previousDay = clone $dateTime;
        $previousDay->sub(new \DateInterval('P1D'));

        $currentDateTimeBestRate =
            $this->getBestRateByCreateDate($dateTime);

        $previousDateTimeBestRate =
            $this->getBestRateByCreateDate($previousDay);

        if (!isset($currentDateTimeBestRate->price)) {
            return;
        }

        if (!isset($previousDateTimeBestRate->price)) {
            return $currentDateTimeBestRate;
        }

        if ($currentDateTimeBestRate->price < $previousDateTimeBestRate->price) {
            return $currentDateTimeBestRate;
        }

        if ($returnEquals && $currentDateTimeBestRate->price == $previousDateTimeBestRate->price) {
            return $currentDateTimeBestRate;
        }

        return;

    }
}
