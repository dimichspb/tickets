<?php

namespace common\Models;

use Yii;
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
 *
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

    public function getRates()
    {
        return $this->hasMany(Rate::className(), ['route' => 'id']);
    }

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
            if (!$route) {
                return parent::beforeSave($insert);
            }
        }
    }

    public static function getRoutesWithOldRate()
    {
        $query = (Route::find()->select('`route`.*, count(`rate`.`id`) as `rates`')->leftJoin('rate', '`rate`.`route`=`route`.`id` AND DATE(`rate`.`create_date`) = CURDATE()')->groupBy('`route`.`id`')->having('`rates` = 0'));

        $result = $query->all();

        if (count($result) > 0) {
            return($result);
        }
    }

    public static function getRoutesWithOldRateByRequestId($requestId)
    {
        $query = (Route::find()->select('`route`.*, `request_to_route`.*, count(`rate`.`id`) as `rates`')->innerJoin('request_to_route', '`request_to_route`.`route` = `route`.`id` AND `request_to_route`.`request`=' . $requestId)->leftJoin('rate', '`rate`.`route`=`route`.`id` AND DATE(`rate`.`create_date`) = CURDATE()')->groupBy('`route`.`id`')->having('`rates` = 0'));

        $result = $query->all();

        if (count($result) > 0) {
            return($result);
        }
    }

    public static function createRoutes($requestId, $limit)
    {
        Route::setLimit($limit);
        if ($requestId) {
            $requests = [Request::getRequestById($requestId)];
        } else {
            $requests = Request::getAllRequests();
        }

        foreach ($requests as $request) {
            Route::createRoutesByRequest($request);
        }
    }

    private static function createRoutesByRequest(Request $request)
    {
        $originPlace = Place::getPlaceById($request->origin);
        $destinationPlace = Place::getPlaceById($request->destination);

        $originCitiesList = $originPlace->getCities();
        $destinationCitiesList = $destinationPlace->getCities();

        $thereStartDateTime = new \DateTime($request->there_start_date);
        $thereEndDateTime = new \DateTime($request->there_end_date);
        $thereEndDateTime->add(new \DateInterval('P1D'));

        $thereDatesList = new \DatePeriod(
            $thereStartDateTime,
            new \DateInterval('P1D'),
            $thereEndDateTime
        );

        if ($request->travel_period_start && $request->travel_period_end) {
            $travelPeriodRange = range($request->travel_period_start, $request->travel_period_end);
        } else {
            $travelPeriodRange = NULL;
        }

        foreach ($originCitiesList as $originCity) {
            foreach ($destinationCitiesList as $destinationCity) {
                foreach ($thereDatesList as $thereDate) {
                    if ($travelPeriodRange) {
                        foreach ($travelPeriodRange as $traverPeriodItem) {
                            $backDate = clone $thereDate;
                            $backDate->add(new \DateInterval('P' . $traverPeriodItem . 'D'));
                            Route::createRoute($request, $originCity, $destinationCity, $thereDate, $backDate);
                        }
                    } else {
                        Route::createRoute($request, $originCity, $destinationCity, $thereDate);
                    }
                }
            }
        }
    }

    private static function createRoute(Request $request, City $originCity, City $destinationCity, \DateTime $thereDate, \DateTime $backDate = null)
    {
        if ($backDate) {
            $route = Route::findOne([
                'origin_city' => $originCity->code,
                'destination_city' => $destinationCity->code,
                'there_date' => $thereDate->format('Y-m-d H:i:s'),
                'back_date' => $backDate->format('Y-m-d H:i:s'),
            ]);
        } else {
            $route = Route::findOne([
                'origin_city' => $originCity->code,
                'destination_city' => $destinationCity->code,
                'there_date' => $thereDate->format('Y-m-d H:i:s'),
            ]);
        }

        if (!$route) {
            $route = new Route();
            $route->origin_city = $originCity->code;
            $route->destination_city = $destinationCity->code;
            $route->there_date = $thereDate->format('Y-m-d H:i:s');
            $route->back_date = $backDate ? $backDate->format('Y-m-d H:i:s') : null;
        }

        if ($route->validate() && $route->save()) {
            $route->link('requests', $request);
            Route::checkLimit();
        }
    }

    private static function setLimit($limit)
    {
        self::$limit = $limit;
    }

    private static function checkLimit()
    {
        if (isset(self::$limit)) {
            if (--self::$limit <= 0) exit();
        }
    }

}
