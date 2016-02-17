<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;


class RequestController extends ActiveController
{
    public $modelClass = 'common\models\Request';

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
}