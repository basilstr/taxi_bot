<?php

namespace app\controllers;

use Yii;
use app\components\BaseController;
use app\models\AccountForm;

class AccountController extends BaseController
{

    public function actionIndex()
    {
        $model = new AccountForm();
        if ($model->load(Yii::$app->request->post())) {
            $model->updateData();
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

}
