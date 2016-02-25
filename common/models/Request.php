<?php

namespace common\Models;

use Yii;
use common\models\Airport;
use common\models\Place;
use common\models\Route;

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
 *
 * @property Place $destination
 * @property Place $origin
 * @property User $user
 */
class Request extends \yii\db\ActiveRecord
{
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

    public function createRoutes()
    {
        $originPlace = Place::findOne($this->origin);
        $destinationPlace = Place::findOne($this->destination);

        $originCitiesList = $originPlace->getCities();
        $destinationCitiesList = $destinationPlace->getCities();

        $thereStartDateTime = new \DateTime($this->there_start_date);
        $thereEndDateTime = new \DateTime($this->there_end_date);
        $thereEndDateTime->add(new \DateInterval('P1D'));

        $thereDatesList = new \DatePeriod(
            $thereStartDateTime,
            new \DateInterval('P1D'),
            $thereEndDateTime
        );

        if ($this->travel_period_start && $this->travel_period_end) {
            $travelPeriodRange = range($this->travel_period_start, $this->travel_period_end);
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
                            $this->createRoute($originCity, $destinationCity, $thereDate, $backDate);
                        }
                    } else {
                        $this->createRoute($originCity, $destinationCity, $thereDate);
                    }
                }
            }
        }
    }

    public function createRoute(City $originCity, City $destinationCity, \DateTime $thereDate, \DateTime $backDate = null)
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

        if ($route->validate() && !$route->save()) {
            $route->link('requests', $this);
        }
    }

    public function getRoutes()
    {
        return $this->hasMany(Route::className(), ['id' => 'route'])->viaTable('request_to_route', ['request' => 'id']);
    }
}
