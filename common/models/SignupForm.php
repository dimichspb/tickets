<?php
namespace common\models;

use common\models\User;
use yii\base\Model;
use Yii;
use common\models\Request;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $first_name;
    public $last_name;
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            [['first_name', 'last_name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'language' => 'Language',
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            $user->first_name = $this->first_name;
            $user->last_name = $this->last_name;
            $user->email = $this->email;
            if (isset($this->password)) {
                $user->setPassword($this->password);
            }
            $user->generateAuthKey();
            if ($user->save()) {
                return $user;
            }
        }
    }
}
