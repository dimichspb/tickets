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

    public static function process($requestId = null, $limit = 1000)
    {
        $mailings = Mailing::getActiveArray();

        foreach ($mailings as $mailing) {
            if (!$mailing->needToProcess()) continue;

            switch ($mailing->code) {
                case 'DRATE':
                    $mailing->processDRate($requestId, $limit);
                    break;
                default:

            }
        }
    }

    private function processDRate($requestId = null, $limit = 1000)
    {
        if ($requestId) {
            $requests = [Request::getRequestById($requestId)];
        } else {
            $requests = Request::getRequestsToMailArray();
        }

        foreach ($requests as $request) {
            $log = '';
            Console::stdout('request: ' . $request->id. '...');

            $betterRates = $request->getBetterRates($limit);
            if (count($betterRates)) {
                $log .= ('better rates: ');

                $bestRatesByPrice = array_slice(ArrayHelper::index($betterRates, null, function($rate) {
                    return $rate->price . ' ' . $rate->currency;
                }), 0, 3);
                $bestRatesByOriginDestination = array_slice(ArrayHelper::index($betterRates, null, [
                    function (Rate $rate) use ($request) {
                        return $rate->getOriginCityName($request->getUserLanguage());
                    },
                    function (Rate $rate) use ($request) {
                        return $rate->getDestinationCityName($request->getUserLanguage());
                    }
                ]),0,3);

                $i = 0;
                $bestPricesIndex = [];
                $bestPricesRates = [];
                foreach ($bestRatesByPrice as $index => $rates) {
                    $bestPricesIndex[] = $index;
                    $bestPricesRates[] = array_slice($rates, 0, 3);
                    $i++;
                }

                $i = 0;
                $bestOriginsIndex = [];
                $bestDestinationsIndex = [];
                $bestOriginsDestinationsRates = [];
                foreach ($bestRatesByOriginDestination as $origin => $destinations) {
                    $bestOriginsIndex[] = $origin;
                    foreach ($destinations as $destination => $rates) {
                        $bestDestinationsIndex[$i][] = $destination;
                        $bestOriginsDestinationsRates[$i][] = array_slice($rates, 0, 3);
                    }
                    $i++;
                }

                $mailingQueue = $this->addToQueue(User::getUserById($request->user), [
                    'originPlace' => $request->getOriginPlaceName(),
                    //'originPlace' => $request->getOriginOne()->getPlaceName(),
                    //'destinationPlace' => $request->getDestinationOne()->getPlaceName(),
                    //'thereDates' => Yii::$app->formatter->format($request->there_start_date, 'date') . ' - ' . Yii::$app->formatter->format($request->there_end_date, 'date'),
                    //'lengthDays' => $request->travel_period_start . ' - ' . $request->travel_period_end,
                    'allRates' => $betterRates,
                    'bestPrice1' => isset($bestPricesIndex[0])? $bestPricesIndex[0] : '',
                    'bestPrices1' => isset($bestPricesRates[0])? $bestPricesRates[0]: [],
                    'bestPrice2' => isset($bestPricesIndex[1])? $bestPricesIndex[1] : '',
                    'bestPrices2' => isset($bestPricesRates[1])? $bestPricesRates[1]: [],
                    'bestPrice3' => isset($bestPricesIndex[2])? $bestPricesIndex[2] : '',
                    'bestPrices3' => isset($bestPricesRates[2])? $bestPricesRates[2]: [],
                    'bestOrigin1' => isset($bestOriginsIndex[0])? $bestOriginsIndex[0] : '',
                    'bestOrigin1Destination1' => isset($bestDestinationsIndex[0][0])? $bestDestinationsIndex[0][0] : '',
                    'bestOrigin1Destinations1' => isset($bestOriginsDestinationsRates[0][0])? $bestOriginsDestinationsRates[0][0]: [],
                    'bestOrigin1Destination2' => isset($bestDestinationsIndex[0][1])? $bestDestinationsIndex[0][1] : '',
                    'bestOrigin1Destinations2' => isset($bestOriginsDestinationsRates[0][1])? $bestOriginsDestinationsRates[0][1]: [],
                    'bestOrigin1Destination3' => isset($bestDestinationsIndex[0][2])? $bestDestinationsIndex[0][2] : '',
                    'bestOrigin1Destinations3' => isset($bestOriginsDestinationsRates[0][2])? $bestOriginsDestinationsRates[0][2]: [],
                    'bestOrigin2' => isset($bestOriginsIndex[1])? $bestOriginsIndex[1] : '',
                    'bestOrigin2Destination1' => isset($bestDestinationsIndex[1][0])? $bestDestinationsIndex[1][0] : '',
                    'bestOrigin2Destinations1' => isset($bestOriginsDestinationsRates[1][0])? $bestOriginsDestinationsRates[1][0]: [],
                    'bestOrigin2Destination2' => isset($bestDestinationsIndex[1][1])? $bestDestinationsIndex[1][1] : '',
                    'bestOrigin2Destinations2' => isset($bestOriginsDestinationsRates[1][1])? $bestOriginsDestinationsRates[1][1]: [],
                    'bestOrigin2Destination3' => isset($bestDestinationsIndex[1][2])? $bestDestinationsIndex[1][2] : '',
                    'bestOrigin2Destinations3' => isset($bestOriginsDestinationsRates[1][2])? $bestOriginsDestinationsRates[1][2]: [],
                    'bestOrigin3' => isset($bestOriginsIndex[2])? $bestOriginsIndex[2] : '',
                    'bestOrigin3Destination1' => isset($bestDestinationsIndex[2][0])? $bestDestinationsIndex[2][0] : '',
                    'bestOrigin3Destinations1' => isset($bestOriginsDestinationsRates[2][0])? $bestOriginsDestinationsRates[2][0]: [],
                    'bestOrigin3Destination2' => isset($bestDestinationsIndex[2][1])? $bestDestinationsIndex[2][1] : '',
                    'bestOrigin3Destinations2' => isset($bestOriginsDestinationsRates[2][1])? $bestOriginsDestinationsRates[2][1]: [],
                    'bestOrigin3Destination3' => isset($bestDestinationsIndex[2][2])? $bestDestinationsIndex[2][2] : '',
                    'bestOrigin3Destinations3' => isset($bestOriginsDestinationsRates[2][2])? $bestOriginsDestinationsRates[2][2]: [],
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
