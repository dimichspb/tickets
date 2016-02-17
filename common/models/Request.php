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
 * @property integer $user
 * @property integer $origin
 * @property integer $destination
 * @property string $there_start_date
 * @property string $there_end_date
 * @property string $back_start_date
 * @property string $back_end_date
 * @property integer $status
 *
 * @property Place $destination0
 * @property Place $origin0
 * @property User $user0
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
            [['create_date', 'there_start_date', 'there_end_date', 'back_start_date', 'back_end_date'], 'safe'],
            [['user', 'origin', 'destination', 'there_start_date', 'there_end_date'], 'required'],
            [['user', 'origin', 'destination', 'status'], 'integer']
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
            'back_start_date' => 'Back Start Date',
            'back_end_date' => 'Back End Date',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDestination0()
    {
        return $this->hasOne(Place::className(), ['id' => 'destination']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrigin0()
    {
        return $this->hasOne(Place::className(), ['id' => 'origin']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser0()
    {
        return $this->hasOne(User::className(), ['id' => 'user']);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->createRoutes();
        }
        return parent::beforeSave($insert);
    }

    private function createRoutes()
    {
        $originPlace = Place::findOne($this->origin);
        $destinationPlace = Place::findOne($this->destination);

        //var_dump($originPlace);
        //var_dump($destinationPlace);

        $originAirportList = $originPlace->getAirports();
        $destinationAirportsList = $destinationPlace->getAirports();

        //var_dump($originAirportList);
        //var_dump($destinationAirportsList);

        $thereDatesList = new \DatePeriod(
            new \DateTime($this->there_start_date),
            new \DateInterval('P1D'),
            new \DateTime($this->there_end_date)
        );
        if ($this->back_start_date && $this->back_end_date) {
            $backDatesList = new \DatePeriod(
                new \DateTime($this->back_start_date),
                new \DateInterval('P1D'),
                new \DateTime($this->back_end_date)
            );
        } else {
            $backDatesList = NULL;
        }

        foreach ($originAirportList as $originAirport) {
            foreach ($destinationAirportsList as $destinationAirport) {
                foreach ($thereDatesList as $thereDate) {
                    if ($backDatesList) {
                        foreach ($backDatesList as $backDate) {
                            $this->createRoute($originAirport, $destinationAirport, $thereDate, $backDate);
                        }
                    } else {
                        $this->createRoute($originAirport, $destinationAirport, $thereDate);
                    }
                }
            }
        }

    }

    private function createRoute(Airport $originAirport, Airport $destinationAirport, \DateTime $thereDate, \DateTime $backDate = null)
    {
        if ($backDate) {
            $route = Route::findOne([
                'origin_airport' => $originAirport->code,
                'destination_airport' => $destinationAirport->code,
                'there_date' => $thereDate->format('Y-m-d H:i:s'),
                'back_date' => $backDate->format('Y-m-d H:i:s'),
            ]);
        } else {
            $route = Route::findOne([
                'origin_airport' => $originAirport->code,
                'destination_airport' => $destinationAirport->code,
                'there_date' => $thereDate->format('Y-m-d H:i:s'),
            ]);
        }

        if (!$route) {
            $route = new Route();
            $route->origin_airport = $originAirport->code;
            $route->destination_airport = $destinationAirport->code;
            $route->there_date = $thereDate->format('Y-m-d H:i:s');
            $route->back_date = $backDate ? $backDate->format('Y-m-d H:i:s') : null;
        }

        return $route->save();
    }
}
