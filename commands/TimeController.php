<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Order;
use app\models\BotSending;


class TimeController extends Controller
{
    // php yii time

    public function actionIndex($id_order)
    {
        sleep(10);
        if($this->sendWaitMessage($id_order, 'Ми все ще шукаємо авто', false)) return ExitCode::OK;

        sleep(30);
        if($this->sendWaitMessage($id_order, 'Не хвилюйтеся, процедура пошуку триває', false)) return ExitCode::OK;

        sleep(50);
        if($this->sendWaitMessage($id_order, '... трішки терпіння ...', false)) return ExitCode::OK;

        sleep(60);
        if($this->sendWaitMessage($id_order, '... ще 30 секунд і готово', false)) return ExitCode::OK;

        sleep(30);
        $this->sendWaitMessage($id_order, 'Нажаль жоден водій не прийняв ваше замовлення', true);

        return ExitCode::OK;
    }
    private function sendWaitMessage($id_order, $msg, $not_found = false)
    {
        /** @var Order $updateOrder */
        $updateOrder = Order::findOne($id_order);
        if($updateOrder) {
            if (empty($updateOrder->id_driver)) {
                BotSending::sendText(0, $updateOrder->id_client, $msg);
                if($not_found){
                    $updateOrder->status = Order::NOT_FOUND;
                    $updateOrder->save();
                }
                return false;
            }
        }
        return true;
    }
}