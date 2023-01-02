<?php

namespace app\components;

use yii\web\Controller;
use app\models\User;

class BaseController extends Controller
{
    private $_user;

    public function init()
    {
        parent::init();
    }

    /**
     * @return User
     */
    public function getUser()
    {
        if (\Yii::$app->user->isGuest) {
            return null;
        }
        if ($this->_user == null) {
            $this->_user = User::findIdentity(\Yii::$app->user->identity->getId());
        }

        return $this->_user;
    }
}