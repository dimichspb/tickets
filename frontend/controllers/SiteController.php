<?php
namespace frontend\controllers;

use common\models\Language;
use Yii;
use common\models\LoginForm;
use common\models\Request;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use common\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Html;
use common\models\User;
use yii\web\Session;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $session = Yii::$app->session;
        $userId = Yii::$app->getUser()->getId();

        $request = new Request();
        if ($request->load(Yii::$app->request->post())) {
            $session->set('newRequestId', $request->id);
            $request->user = $request->user? $request->user: $userId? $userId: '1';

            if ($request->validate() && $request->save()) {
                if (Yii::$app->getUser()->isGuest) {
                    Yii::$app->session->setFlash('success', 'Please signup or ' . Html::a('login', ['site/login']) . ' before request to let me know your email');
                    return $this->redirect(['site/signup']);
                }
                Yii::$app->session->setFlash('success', 'The request has been placed successfully');
                return $this->redirect(['site/done']);
            }
        }

        $today = new \DateTime();
        $there_start_date = clone $today;
        $there_start_date->add(new \DateInterval('P1M'));
        $there_end_date = clone $today;
        $there_end_date->add(new \DateInterval('P1M7D'));

        $request->there_start_date = $there_start_date->format('Y-m-d');
        $request->there_end_date = $there_end_date->format('Y-m-d');
        $request->travel_period_start = 5;
        $request->travel_period_end = 10;

        return $this->render('index', [
            'model' => $request,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionDone()
    {
        return $this->render('done');
    }

    public function actionSignup()
    {
        $session = Yii::$app->session;

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                $user->sendSignupEmail();
                if ($session->has('newRequestId')) {
                    $request = Request::getRequestById($session->get('newRequestId'));
                    $request->user = $user->id;
                    $request->save();
                }
                if (Yii::$app->getUser()->login($user)) {
                    $session->remove('newRequestId');
                    Yii::$app->session->setFlash('success', 'You are successfully logged in');
                    return $this->redirect(['site/done']);
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if (Yii::$app->request->get('auth_key')) {
            if ($model->login()) {
                Yii::$app->session->setFlash('success', 'You are successfully logged in');
                return $this->goBack();
            } else {
                return $this->redirect(['site/error']);
            }
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($user = User::findByEmail($model->email)) {
                    $user->sendLoginEmail();
                    Yii::$app->session->setFlash('success', 'The authorization email has been sent');
                    return $this->redirect(['site/index']);
                }
                Yii::$app->session->setFlash('error', 'Could not find user with specified email');
            }
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        Yii::$app->session->setFlash('success', 'You are successfully logged out');
        return $this->goHome();
    }

    public function actionError()
    {
        Yii::$app->session->setFlash('error', 'You are successfully logged out');
        return $this->render(['error']);
    }

    public function actionLang()
    {
        $languageCode = Yii::$app->request->get('lang');

        $languages = Language::getLanguagesArray();
        if ($languageCode && ArrayHelper::keyExists($languageCode, $languages)) {
            $session = new Session();
            $session->set('language', $languageCode);
        }
        return $this->goBack();
    }
}
