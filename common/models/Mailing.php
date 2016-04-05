<?php

namespace common\models;

use Yii;
use yii\debug\models\search\Mail;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

/**
 * This is the model class for table "mailing".
 *
 * @property string $code
 * @property string $name
 * @property integer $status
 * @property string $processed_date
 * @property integer $process_period
 * @property string $server
 *
 * @property MailingConfiguration[] $mailingConfigurations
 * @property MailingQueue[] $mailingQueues
 * @property MailingToMailingType[] $mailingToMailingTypes
 * @property VariableScope[] $variableScopes
 */
class Mailing extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 0;
    const STATUS_INACTIVE = 1;
    const STATUS_DELETED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mailing';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'name'], 'required'],
            [['status'], 'integer'],
            [['code'], 'string', 'max' => 5],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'code' => 'Code',
            'name' => 'Name',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingConfigurations()
    {
        return $this->hasMany(MailingConfiguration::className(), ['mailing' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingQueues()
    {
        return $this->hasMany(MailingQueue::className(), ['mailing' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingToMailingTypes()
    {
        return $this->hasMany(MailingToMailingType::className(), ['mailing' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVariableScopes()
    {
        return $this->hasMany(VariableScope::className(), ['mailing' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getActive()
    {
        return Mailing::find()
            ->where([
                'status' => Mailing::STATUS_ACTIVE,
            ]);
    }

    /**
     * @return Mailing[]
     */
    public static function getActiveArray()
    {
        return Mailing::getActive()->all();
    }

    public static function process()
    {
        $mailings = Mailing::getActiveArray();

        foreach ($mailings as $mailing) {
            if (!$mailing->needToProcess()) continue;

            switch ($mailing->code) {
                case 'DRATE':
                    $mailing->processDRate();
                    break;
                default:

            }
        }
    }

    private function processDRate()
    {
        $today = new \DateTime();
        $requests = Request::getRequestsToMailArray();

        foreach ($requests as $request) {
            $log = ('request: ' . $request->id. '...');
            $betterRates = [];
            /*
            $routes = $request->getRoutesArray();
            foreach ($routes as $route) {
                if ($rate = $route->getBetterRate($today, !$request->isMailingProcessed())) {
                    $betterRates[] = [
                        'origin_city' => $rate->originCity->getCityDescByLanguage($request->getUserLanguage())->name,
                        'destination_city' => $rate->destinationCity->getCityDescByLanguage($request->getUserLanguage())->name,
                        'there_date' => $rate->there_date,
                        'back_date' => $rate->back_date,
                        'airline' => $rate->airline,
                        'flight' => $rate->flight_number,
                        'currency' => $rate->currency,
                        'price' => $rate->price,
                    ];
                }
            }

            foreach ($request->getBetterRates() as $rate) {
                $betterRates[] = [
                    'id' => $rate->id,
                    'origin_city' => $rate->originCity->getCityDescByLanguage($request->getUserLanguage())->name,
                    'destination_city' => $rate->destinationCity->getCityDescByLanguage($request->getUserLanguage())->name,
                    'there_date' => $rate->there_date,
                    'back_date' => $rate->back_date,
                    'airline' => $rate->airline,
                    'flight' => $rate->flight_number,
                    'currency' => $rate->currency,
                    'price' => $rate->price,
                ];
            }
*/
            $betterRates = $request->getBetterRates();
            if (count($betterRates)) {
                $log .= ('better rates: ');

                //$betterRates = ArrayHelper::index($betterRates, function ($element) {
                //    return $element['origin_city'] . '-' . $element['destination_city'];
                //});
                $bestRatesByPrice = ArrayHelper::index($betterRates, 'price');
                $bestRatesByOrigin = ArrayHelper::index($betterRates, 'origin_city');
                $bestRatesByDestination = ArrayHelper::index($betterRates, 'destination_city');

                $mailingQueue = $this->addToQueue(User::getUserById($request->user), [
                    'rates' => $betterRates,
                    'bestPrice' => $bestRatesByPrice,
                    'bestOrigin' => $bestRatesByOrigin,
                    'bestDestination' => $bestRatesByDestination,
                    //'allrates' => [
                    //    'data' => serialize($betterRates),
                    //],
                ]);
                foreach ($betterRates as $rate) {
                    $requestMailingRate = new RequestMailingRate();
                    $requestMailingRate->request = $request->id;
                    $requestMailingRate->mailing_queue = $mailingQueue->id;
                    $requestMailingRate->rate = $rate->id;
                    $requestMailingRate->save();
                    $log .= ($rate->price. ',');
                }
            } else {
                $log .= ('no better rates');
            }
            Console::stdout($log . PHP_EOL);
        }
    }

    /**
     * @param User $user
     * @param array $details
     * @param \DateTime|null $plannedDate
     * @return MailingQueue
     */
    public function addToQueue(User $user, array $details, \DateTime $plannedDate = null)
    {
        $server = Server::getServer($this->getMailingType());

        $queue = new MailingQueue();
        if ($plannedDate) {
            $queue->planned_date = $plannedDate->format('Y-m-d H:i:s');
        }
        $queue->mailing = $this->code;
        $queue->user = $user->id;
        $queue->server = $server->code;
        $queue->save();

        foreach ($queue->getMailingDetailsViaMailingTypesArray() as $mailingDetail) {
            $mailingConfiguration = $queue->getMailingConfiguration($mailingDetail);
            $queueDetail = new MailingQueueDetail();
            $queueDetail->mailing_queue = $queue->id;
            $queueDetail->mailing_detail = $mailingDetail->code;

            $queueDetail->value = $mailingConfiguration->getValue($user->getLanguageOne(), ArrayHelper::merge([
                'user' => [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                ],
            ], $details));
            $queueDetail->save();
        }
        return $queue;
    }

    public function needToProcess()
    {
        return
            (is_null($this->processed_date)) || ((time() - strtotime($this->processed_date)) > $this->process_period);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingTypes()
    {
        return $this->hasMany(MailingType::className(), ['code' => 'mailing_type'])->via('mailingToMailingTypes');
    }

    /**
     * @return MailingType
     */
    public function getMailingType()
    {
        return $this->getMailingTypes()->one();
    }

    /**
     * @param $mailingCode
     * @return Mailing
     */
    public static function getMailingByCode($mailingCode)
    {
        return Mailing::find()->where([
                'code' => $mailingCode,
            ])->one();
    }
}
