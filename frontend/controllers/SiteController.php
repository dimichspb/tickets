<?php
namespace frontend\controllers;

use Yii;
use common\Models\LoginForm;
use common\Models\Request;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

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
        $request = new Request();
        if ($request->load(Yii::$app->request->post())) {
            $request->user = $request->user? $request->user: '1';
            if ($request->validate() && $request->save()) {
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
}
