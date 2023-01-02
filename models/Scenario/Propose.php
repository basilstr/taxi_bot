<?php
namespace app\models\Scenario;

use yii\base\Model;
use app\models\BotUsers;
use app\models\Order;
use app\models\BotSending;

class Propose extends Model
{
    const D_LAT = 0.000009; // 1 метр в градусній мірі
    const D_LON = 0.0000138; // 1 метр в градусній мірі

    private $lat;
    private $lon;

    /**
     * розсилка пропозицій водіям
     *
     * @param $order
     * @return bool
     */
    public static function sendPropose($order)
    {
        BotSending::sendText(0, $order->id_client, \Yii::t('app', 'Ваше замовлення прийнято та буде запропоновано водіям'));

        $propose = new self();
        $propose->lat = $order->lat;
        $propose->lon = $order->lon;
        $listDriver = $propose->listFreeDriver();
        /** @var BotUsers $driver */
        foreach ($listDriver as $driver) {
            BotSending::sendLocation($order->id_client, $driver->id, $propose->lat, $propose->lon);
            // вайбер сам робить зворотню геолокацію
            if ($order->address && $driver->bot->type != 'viber') BotSending::sendText($order->id_client, $driver->id, $order->address);
            $menu = [
                ['btn' => '/btn-order-accept-' . $order->id, 'text' => \Yii::t('app', 'ПРИЙНЯТИ ЗАМОВЛЕННЯ')],
            ];
            BotMenu::putMenuInline($driver->id, $menu, $order->id_client);
            BotSending::sendText($order->id_client, $driver->id, \Yii::t('app', '*я чекаю вашого рішення*'));
        }
        // затримка 10 сек
        shell_exec("php " . \Yii::getAlias('@app') . "/yii time ".$order->id." > /dev/null &");
        return true;
    }

    private function listFreeDriver()
    {
        // перелік водіїв, які останніх 15 хвилин отримали замовлення
        $id_busy_driver = Order::find()
            ->where(['>', 'id_driver', 0])
            ->andWhere(['status' => Order::ISSUED])
            ->andWhere('create_dt > (NOW() - INTERVAL 15 MINUTE)')
            ->select('id_driver')
            ->column();

        $query = BotUsers::find()
            ->where(['current_type' => BotUsers::DRIVER])
            ->andWhere(['>', 'lat', $this->lat - self::D_LAT * 10000])
            ->andWhere(['<', 'lat', $this->lat + self::D_LAT * 10000])
            ->andWhere(['>', 'lon', $this->lon - self::D_LON * 10000])
            ->andWhere(['<', 'lon', $this->lon + self::D_LON * 10000])
            ->andWhere(['NOT IN', 'id', $id_busy_driver])
            ->andWhere('dt_ban < NOW() OR dt_ban IS NULL');
        return $query->all();
    }
}