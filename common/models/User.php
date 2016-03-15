<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $language
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property string $access_token
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['auth_key', 'email'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['first_name', 'last_name', 'password_hash', 'password_reset_token', 'email', 'access_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['language'], 'string', 'max' => 5],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => Yii::t('app', 'Email'),
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'access_token' => 'Access Token',
            'language' => 'Language',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $email
     * @return User
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username
     *
     * @param string $authKey
     * @return static|null
     */
    public static function findByAuthKey($authKey)
    {
        return static::findOne(['auth_key' => $authKey, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates access token  and sets it to the model

     */
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Do some changes before save user into DB
     *
     * @param $insert boolean
     * @return boolean
     */
    public function beforeSave($insert)
    {
        $today = new \DateTime();
        $this->updated_at = $today->format('Y-m-d H:i:s');
        $this->setPassword('');
        if ($insert) {
            $this->generateAccessToken(); //Set access_token before save new user
            $this->created_at = $today->format('Y-m-d H:i:s');
            $this->language = Yii::$app->language;
        }
        return parent::beforeSave($insert);
    }

    /**
     * @param $userId
     * @return User
     */
    public static function getUserById($userId)
    {
        return User::findOne([
            'id' => $userId
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['code' => 'language']);
    }

    /**
     * @return Language
     */
    public function getLanguageOne()
    {
        return $this->getLanguage()->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingQueues()
    {
        return $this->hasMany(MailingQueue::className(), ['user' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequests()
    {
        return $this->hasMany(Request::className(), ['user' => 'id']);
    }

    public function sendSignupEmail()
    {
        $mailing = Mailing::getMailingByCode('SGNUP');
        $mailingQueue = $mailing->addToQueue($this, [
            'link' => Html::a('TicketTracker.com', 'http://' . Yii::$app->params['frontend']['domain']),
        ]);
        return $mailingQueue->send();
    }

    public function sendLoginEmail()
    {
        $mailing = Mailing::getMailingByCode('LOGIN');
        $mailingQueue = $mailing->addToQueue($this, [
            'link' => Html::a('TicketTracker.com', 'http://' . Yii::$app->params['frontend']['domain'] . '/login?auth_key=' . $this->auth_key),
        ]);
        return $mailingQueue->send();
    }

}
