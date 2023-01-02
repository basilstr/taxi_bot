<?php

namespace app\controllers;

use app\models\viberApi\BotSendingViber;
use Yii;
use app\models\Order;
use yii\filters\VerbFilter;
use app\components\BaseController;
use app\models\BotSending;
use app\models\BotUsers;
use app\models\Bots;
use app\models\viberApi\ViberBot;
use app\models\viberApi\Api\Message\Text;
use app\models\viberApi\Api\Keyboard;
use app\models\viberApi\Api\Keyboard\Button;

/**
 * BotsController implements the CRUD actions for Bots model.
 */
class TestController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Bots models.
     * @return mixed
     */
    public function actionIndex()
    {
        $botDB = Bots::findOne(1);

        $botSender = BotSendingViber::getSender(0);

        $bot = new ViberBot($botDB);
        p($bot->getClient()->sendMessage(
            (new Text())
                ->setSender($botSender)
                ->setReceiver('rRrXDxmQaQSfTlsSFNdf6g==')
                ->setText('MENU')
                ->setMinApiVersion(4)
                ->setKeyboard(
                    (new Keyboard())
                        ->setInputFieldState('hidden')
                        ->setBgColor('#ffffff')
                        ->setButtons([
                            (new Button())
                                ->setSilent(true)
                                ->setColumns(4)
                                //->setBgColor('#328fcd')
                                ->setTextSize('large')
                                ->setTextHAlign('center')
                                ->setActionType('reply')
                                ->setActionBody('text')
                                ->setImage('https://taxi-m.pp.ua/img/btn_4.png')
                                ->setText('<b><font color="#ffffff">' . \Yii::t('app', 'замовити авто') . '</font></b>'),
                            (new Button())
                                ->setSilent(true)
                                ->setColumns(2)
                                //->setBgColor('#328fcd')
                                ->setTextSize('large')
                                ->setTextHAlign('center')
                                ->setActionType('reply')
                                ->setActionBody('text')
                                ->setImage('https://taxi-m.pp.ua/img/btn_2.png')
                                //->setImage('')
                                ->setText('<b><font color="#ffffff">' . \Yii::t('app', 'змінити авто') . '</font></b>'),
                        ])
                )
        ));
    }

}
