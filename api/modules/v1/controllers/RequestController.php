<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\rest\CreateAction;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;


class RequestController extends ActiveController
{
    public $modelClass = 'common\models\Request';
    private $createActionDetails;

    private $accessRules = [
        'index' => 'getRequestsList',
        'view' => 'getRequestDetails',
        'create' => 'createRequestDetails',
        'update' => 'updateRequestDetails',
        'delete' => 'deleteRequestDetails',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON; //setting JSON as default reply
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];

        return $behaviors;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        if (!isset($this->accessRules[$action]) || !Yii::$app->user->can($this->accessRules[$action])) {
            throw new ForbiddenHttpException;
        }
    }

    public function actions()
    {
        $actions = parent::actions();
        $this->createActionDetails = $actions['create'];
        unset($actions['create']);
        return $actions;
    }

    public function actionCreate()
    {
        $createActionClassName = $this->createActionDetails['class'];
        $createActionModelClass = $this->createActionDetails['modelClass'];
        $createActionCheckAccess = $this->createActionDetails['checkAccess'];
        $createActionScenario = $this->createActionDetails['scenario'];

        $createAction = new $createActionClassName('create', $this, [
            'modelClass' => $createActionModelClass,
            'checkAccess' => $createActionCheckAccess,
            'scenario' => $createActionScenario,
        ]);

        $model = $createAction->run();
        $model->save();
        $model->createRoutes();

        return $model;
    }
}