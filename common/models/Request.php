<?php

namespace common\models;

use Yii;
use common\models\Airport;
use common\models\Place;
use common\models\Route;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\db\Query;

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
 * @property integer $route_offset
 * @property integer $rate_offset
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
     * @return Place
     */
    public function getDestinationOne()
    {
        return $this->getDestination()->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrigin()
    {
        return $this->hasOne(Place::className(), ['id' => 'origin']);
    }

    /**
     * @return Place
     */
    public function getOriginOne()
    {
        return $this->getOrigin()->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRequestOriginCities()
    {
        return $this->hasMany(RequestOriginCity::className(), ['request' => 'id']);
    }

    /**
     * @return RequestOriginCity[]
     */
    public function getRequestOriginCitiesAll()
    {
        return $this->getRequestOriginCities()->all();
    }

    /**
     * @return ActiveQuery
     */
    public function getRequestOriginCitiesNew()
    {
        return $this->getRequestOriginCities()->where(['status' => RequestOriginCity::STATUS_NEW]);
    }

    /**
     * @return RequestOriginCity[]
     */
    public function getRequestOriginCitiesNewAll()
    {
        return $this->getRequestOriginCitiesNew()->all();
    }

    /**
     * @return ActiveQuery
     */
    public function getOriginCities()
    {
        return $this->hasMany(City::className(), ['code' => 'city'])->via('requestOriginCities');
    }

    /**
     * @return City[]
     */
    public function getOriginCitiesAll()
    {
        return $this->getOriginCities()->all();
    }

    /**
     * @return ActiveQuery
     */
    public function getOriginCitiesNew()
    {
        return $this->hasMany(City::className(), ['code' => 'city'])->via('requestOriginCitiesNew');
    }

    /**
     * @return City[]
     */
    public function getOriginCitiesNewAll()
    {
        return $this->getOriginCitiesNew()->all();
    }

    /**
     * @return ActiveQuery
     */
    public function getRequestDestinationCities()
    {
        return $this->hasMany(RequestDestinationCity::className(), ['request' => 'id']);
    }

    /**
     * @return RequestDestinationCity[]
     */
    public function getRequestDestinationCitiesAll()
    {
        return $this->getRequestDestinationCities()->all();
    }

    /**
     * @return ActiveQuery
     */
    public function getRequestDestinationCitiesNew()
    {
        return $this->getRequestDestinationCities()->where(['status' => RequestDestinationCity::STATUS_NEW]);
    }

    /**
     * @return RequestDestinationCity[]
     */
    public function getRequestDestinationCitiesNewAll()
    {
        return $this->getRequestDestinationCitiesNew()->all();
    }

    /**
     * @return ActiveQuery
     */
    public function getDestinationCities()
    {
        return $this->hasMany(City::className(), ['code' => 'city'])->via('requestDestinationCities');
    }

    /**
     * @return City[]
     */
    public function getDestinationCitiesAll()
    {
        return $this->getDestinationCities()->all();
    }

    /**
     * @return ActiveQuery
     */
    public function getDestinationCitiesNew()
    {
        return $this->hasMany(City::className(), ['code' => 'city'])->via('requestDestinationCitiesNew');
    }

    /**
     * @return City[]
     */
    public function getDestinationCitiesNewAll()
    {
        return $this->getDestinationCitiesNew()->all();
    }

    /**
     * @return ActiveQuery
     */
    public function getRequestThereDates()
    {
        return $this->hasMany(RequestThereDate::className(), ['request' => 'id']);
    }

    /**
     * @return RequestThereDate[]
     */
    public function getRequestThereDatesAll()
    {
        return $this->getRequestThereDates()->all();
    }

    /**
     * @return ActiveQuery
     */
    public function getRequestThereDatesNew()
    {
        return $this->getRequestThereDates()->where(['status' => RequestThereDate::STATUS_NEW]);
    }

    /**
     * @return RequestThereDate[]
     */
    public function getRequestThereDatesNewAll()
    {
        return $this->getRequestThereDatesNew()->all();
    }


    /**
     * @return ActiveQuery
     */
    public function getRequestTravelPeriods()
    {
        return $this->hasMany(RequestTravelPeriod::className(), ['request' => 'id']);
    }

    /**
     * @return RequestTravelPeriod[]
     */
    public function getRequestTravelPeriodsAll()
    {
        return $this->getRequestTravelPeriods()->all();
    }

    /**
     * @return ActiveQuery
     */
    public function getRequestTravelPeriodsNew()
    {
        return $this->getRequestTravelPeriods()->where(['status' => RequestTravelPeriod::STATUS_NEW]);
    }

    /**
     * @return RequestTravelPeriod[]
     */
    public function getRequestTravelPeriodsNewAll()
    {
        return $this->getRequestTravelPeriodsNew()->all();
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
     * @param integer $limit
     * @return \yii\db\ActiveQuery
     */
    public function getRates($limit = null)
    {
        //$result = $this->hasMany(Rate::className(), ['route' => 'id'])->via('routes')->orderBy('price');

        $result = Rate::find()
            ->innerJoin('route', ['route.id' => new Expression('`rate`.`route`')])
            ->innerJoin('request_to_route', ['request_to_route.route' => new Expression('`route`.`id`')])
            ->innerJoin('request', ['request.id' => new Expression('`request_to_route`.`request`')])
            ->where(['request.id' => $this->id]);

        if ($limit) {
            $result->limit($limit)->offset($this->rate_offset);
        }

        //var_dump($result->createCommand()->rawSql);
        return $result;
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

    /**
     * @param integer $limit
     * @return Rate[]
     */
    public function getRatesAll($limit = null)
    {
        $result = $this->getRates($limit)->all();

        if (count($result) === 0) {
            $this->rate_offset = 0;
        } else {
            $this->rate_offset = $this->rate_offset + count($result);
        }
        $this->update(false, ['rate_offset']);

        return $result;
    }

    /**
     * @param integer $limit
     * @return array
     */
    public function getBetterRates($limit = 1000)
    {
        $mailedRates = $this->getMailedRatesAll($limit);
        Console::stdout('mailed rates: ' . count($mailedRates));

        $allRates = $this->getRates(count($mailedRates)? count($mailedRates): $limit)->offset(0)->orderBy('price')->all();

        Console::stdout(', all rates: ' . count($allRates));

        $mailedRatesMin = count($mailedRates)? min(ArrayHelper::map($mailedRates, 'id', 'price')): null;

        $betterRates = array_filter($allRates, function (Rate $rate) use ($mailedRatesMin) {
            return is_null($mailedRatesMin) || $rate->price < $mailedRatesMin;
        });

        Console::stdout(', better rates: ' . count($betterRates). PHP_EOL);

        return $betterRates;
    }

    /**
     * @param integer $limit
     * @return ActiveQuery
     */
    public function getMailedRates($limit = null)
    {
        $result = $this->hasMany(Rate::className(), ['id' => 'rate'])->viaTable('request_mailing_rate', ['request' => 'id'])->orderBy('price');
        if ($limit) {
            $result->limit($limit);
        }

        return $result;
    }

    /**
     * @param integer $limit
     * @return Rate[]
     */
    public function getMailedRatesAll($limit = null)
    {
        return $this->getMailedRates($limit)->all();
    }


    /**
     * Method adds limited by $limit number of Routes of the specified $requestId
     *
     * @param $requestId
     * @param $limit
     */
    public static function process($requestId, $limit = 1000)
    {
        if ($requestId) {
            $requests = [Request::getRequestById($requestId)];
        } else {
            $requests = Request::getAllRequests();
        }

        foreach ($requests as $request) {
            $request->createRoutes($limit);
        }
    }

    public function createRoutes($limit = 1000)
    {
        $log = 'Request: ' . $this->id . '...';

        $this->addSubTables();

        $routesCreated = $this->createNewRoutes($limit);

        $log .= $routesCreated . ' routes created/linked';
        Console::stdout($log . PHP_EOL);
    }

    public function createNewRoutes($limit = 1000)
    {
        $routesCreated = 0;

        $query = (new Query())
            ->select([
                'originCity' => 'origin_city.city',
                'destinationCity' => 'destination_city.city',
                'thereDate' => 'there_date.date',
                'travelPeriod' => 'travel_period.period',
            ])
            ->from([
                'origin_city' => 'request_origin_city',
                'destination_city' => 'request_destination_city',
                'there_date' => 'request_there_date',
                'travel_period' => 'request_travel_period',
            ])
            ->where([
                'origin_city.request' => $this->id,
                'destination_city.request' => $this->id,
                'there_date.request' => $this->id,
                'travel_period.request' => $this->id,
            ])
            ->limit($limit)
            ->offset($this->route_offset);

        foreach ($query->all() as $row) {
            $originCity = City::getCityByCode($row['originCity']);
            $destinationCity = City::getCityByCode($row['destinationCity']);
            $thereDate = new \DateTime($row['thereDate']);
            $travelPeriod = $row['travelPeriod'];
            if ($this->createRoute($originCity, $destinationCity, $thereDate, $travelPeriod)) {
                $routesCreated++;
            }
        }

        $this->route_offset = $this->route_offset + $limit;
        $this->update(false, ['route_offset']);
        return $routesCreated;
    }

    public function createRoute(City $originCity, City $destinationCity, \DateTime $thereDate, $travelPeriod)
    {
        if ($travelPeriod) {
            $backDate = new \DateTime($thereDate->format('Y-m-d H:i:s'));
            $travelPeriod = new \DateInterval('P' . $travelPeriod . 'D');
            $backDate->add($travelPeriod);
        }

        $route = Route::find()
            ->where([
                'origin_city' => $originCity->code,
                'destination_city' => $destinationCity->code,
                'there_date' => $thereDate->format('Y-m-d H:i:s'),
                'back_date' => isset($backDate) ? $backDate->format('Y-m-d H:i:s') : null,
            ])->one();

        if (!$route) {
            $route = new Route();
            $route->origin_city = $originCity->code;
            $route->destination_city = $destinationCity->code;
            $route->there_date = $thereDate->format('Y-m-d H:i:s');
            $route->back_date = isset($backDate) ? $backDate->format('Y-m-d H:i:s') : null;
            if ($route->isNewRecord) {
                $route->save();
            }
        }

        if (!$route->getRequests()->exists()) {
            $route->link('requests', $this);
            return $route;
        }
    }

    public function addSubTables()
    {

        $originCitiesList = $this->getOriginOne()->getCities();

        foreach ($originCitiesList as $originCity) {
            $requestOriginCity = RequestOriginCity::findOne(['request' => $this->id, 'city' => $originCity->code]);
            if (!$requestOriginCity) {
                $requestOriginCity = new RequestOriginCity();
                $requestOriginCity->request = $this->id;
                $requestOriginCity->city = $originCity->code;
                $requestOriginCity->save();
            }
        }

        $destinationCitiesList = $this->getDestinationOne()->getCities();

        foreach ($destinationCitiesList as $destinationCity) {
            $requestDestinationCity = RequestDestinationCity::findOne(['request' => $this->id, 'city' => $destinationCity->code]);
            if (!$requestDestinationCity) {
                $requestDestinationCity = new RequestDestinationCity();
                $requestDestinationCity->request = $this->id;
                $requestDestinationCity->city = $destinationCity->code;
                $requestDestinationCity->save();
            }
        }

        $thereStartDateTime = new \DateTime($this->there_start_date);
        $thereEndDateTime = new \DateTime($this->there_end_date);
        $thereEndDateTime->add(new \DateInterval('P1D'));

        $thereDatesPeriod = new \DatePeriod(
            $thereStartDateTime,
            new \DateInterval('P1D'),
            $thereEndDateTime
        );

        foreach ($thereDatesPeriod as $thereDate) {
            $requestThereDate = RequestThereDate::findOne(['request' => $this->id, 'date' => $thereDate->format('Y-m-d H:i:s')]);
            if (!$requestThereDate) {
                $requestThereDate = new RequestThereDate();
                $requestThereDate->request = $this->id;
                $requestThereDate->date = $thereDate->format('Y-m-d H:i:s');
                $requestThereDate->save();
            }
        }

        if ($this->travel_period_start && $this->travel_period_end) {
            $travelPeriodRange = range($this->travel_period_start, $this->travel_period_end);
        } else {
            $travelPeriodRange = [0];
        }

        foreach ($travelPeriodRange as $travelPeriod) {
            $requestTravelPeriod = RequestTravelPeriod::findOne(['request' => $this->id, 'period' => $travelPeriod]);
            if (!$requestTravelPeriod) {
                $requestTravelPeriod = new RequestTravelPeriod();
                $requestTravelPeriod->request = $this->id;
                $requestTravelPeriod->period = $travelPeriod;
                $requestTravelPeriod->save();
            }
        }
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
