<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "server".
 *
 * @property string $code
 * @property string $name
 * @property integer $status
 *
 * @property MailingQueue[] $mailingQueues
 * @property ServerConfiguration[] $serverConfigurations
 * @property ServerToServerType[] $serverToServerTypes
 */
class Server extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'server';
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
    public function getMailingQueues()
    {
        return $this->hasMany(MailingQueue::className(), ['server' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServerConfigurations()
    {
        return $this->hasMany(ServerConfiguration::className(), ['server' => 'code']);
    }

    /**
     * @param ServerDetail $serverDetail
     * @return ServerConfiguration
     */
    public function getServerConfiguration(ServerDetail $serverDetail)
    {
        return $this->getServerConfigurations()
            ->where([
                'server_detail' => $serverDetail->code,
            ])
            ->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServerToServerTypes()
    {
        return $this->hasMany(ServerToServerType::className(), ['server' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServerTypes()
    {
        return $this->hasMany(ServerType::className(), ['code' => 'server_type'])->via('serverToServerTypes');
    }

    /**
     * @return ServerType
     */
    public function getServerType()
    {
        return $this->getServerTypes()->one();
    }

    /**
     * @param MailingType $mailingType
     * return Server
     */
    public static function getServer(MailingType $mailingType)
    {
        return $mailingType->serverType->getServers()->one();
    }

    public function getHost()
    {
        return $this->getServerConfiguration(ServerDetail::getServerDetailByCode('HOST'))->value;
    }

    public function getPort()
    {
        return $this->getServerConfiguration(ServerDetail::getServerDetailByCode('PORT'))->value;
    }

    public function getEncryption()
    {
        return $this->getServerConfiguration(ServerDetail::getServerDetailByCode('CRYP'))->value;
    }

    public function getUsername()
    {
        return $this->getServerConfiguration(ServerDetail::getServerDetailByCode('USER'))->value;
    }

    public function getPassword()
    {
        return $this->getServerConfiguration(ServerDetail::getServerDetailByCode('PASS'))->value;
    }

    public function getAuth()
    {
        return $this->getServerConfiguration(ServerDetail::getServerDetailByCode('AUTH'))->value;
    }

    public function sendSMTP(MailingQueue $mailingQueue)
    {
        $transport = \Swift_SmtpTransport::newInstance($this->getHost(), $this->getPort(), $this->getEncryption());

        if ($this->getAuth() === 'true') {
            $transport
                ->setUsername($this->getUsername())
                ->setPassword($this->getPassword());
        }

        $logger = new \Swift_Plugins_Loggers_EchoLogger();
        $mailer = \Swift_Mailer::newInstance($transport);
        $mailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($logger));

        $message = \Swift_Message::newInstance($mailingQueue->getSubject())
            ->setFrom([
                $mailingQueue->getFromAddress() => $mailingQueue->getFromName()
            ])
            ->setTo([
                $mailingQueue->getToAddress() => $mailingQueue->getToName()
            ])
            ->setBody($mailingQueue->getBody(), 'text/html');

        $result = $mailer->send($message);

        if ($result) {
            MailingQueueLog::log($mailingQueue, 'Success, result: true');
        } else {
            MailingQueueLog::log($mailingQueue, $logger->dump());
        }

        return isset($result)? $result: false;
    }
}
