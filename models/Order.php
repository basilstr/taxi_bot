<?php

namespace app\models;

use Yii;
use app\models\Scenario\Propose;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int|null $id_client хто створив замовлення
 * @property int|null $id_driver хто виконує замовлення
 * @property int|null $status статус замовлення
 * @property string|null $address адреса замовлення, якщо вдалось встановити
 * @property float|null $lat координати замовлення
 * @property float|null $lon координати замовлення
 * @property string $create_dt
 *
 * @property BotUsers $client
 * @property BotUsers $driver
 */
class Order extends \yii\db\ActiveRecord
{
    const CREATED = 1; // створено
    const ISSUED = 2;  // видано
    const REJECT_DRIVER = 3;  // скасовано водієм
    const REJECT_CLIENT = 4;  // скасовано пасажиром
    const NOT_FOUND = 5;  // жоден водій не зголосився на замовлення
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_client', 'id_driver', 'status'], 'integer'],
            [['address'], 'string'],
            [['lat', 'lon'], 'number'],
            [['create_dt'], 'safe'],
            [['id_client'], 'exist', 'skipOnError' => true, 'targetClass' => BotUsers::className(), 'targetAttribute' => ['id_client' => 'id']],
            [['id_driver'], 'exist', 'skipOnError' => true, 'targetClass' => BotUsers::className(), 'targetAttribute' => ['id_driver' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_client' => 'Пасажир',
            'id_driver' => 'Водій',
            'status' => 'Статус',
            'address' => 'Адреса',
            'lat' => 'Lat',
            'lon' => 'Lon',
            'create_dt' => 'Створено',
        ];
    }

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(BotUsers::className(), ['id' => 'id_client']);
    }

    /**
     * Gets query for [[Driver]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDriver()
    {
        return $this->hasOne(BotUsers::className(), ['id' => 'id_driver']);
    }

    public static function listStatuses()
    {
        return [
            self::CREATED => 'створено',
            self::ISSUED => 'видано',
            self::REJECT_DRIVER => 'скасовано водієм',
            self::REJECT_CLIENT => 'скасовано пасажиром',
            self::NOT_FOUND => 'жоден водій не зголосився на замовлення',
        ];
    }

    public static function addOrder($id_client, $lat, $lon)
    {
        // перевіряємо, чи було створено замовлення хвилину тому
        if(self::find()
            ->where(['id_client' => $id_client])
            ->andWhere(['status' => Order::CREATED])
            ->andWhere('create_dt > (NOW() - INTERVAL 1 MINUTE)')
            ->exists()){
            BotSending::sendText(0, $id_client, \Yii::t('app', 'Будьте терплячі, ми намагаємось знайти авто'));
            return false;
        }

        // перевіряємо, чи замовлення автивне 10 хв
        if(self::find()
            ->where(['id_client' => $id_client])
            ->andWhere(['status' => Order::ISSUED])
            ->andWhere(['>',  'id_driver', 0])
            ->andWhere('create_dt > (NOW() - INTERVAL 10 MINUTE)')
            ->exists()){
            BotSending::sendText(0, $id_client, \Yii::t('app', 'Будьте терплячі, авто вже їде до вас. Ви можете написати водієві у цей чат'));
            return false;
        }

        $order = new self();
        $order->id_client = $id_client;
        $order->lat = $lat;
        $order->lon = $lon;
        $order->status = self::CREATED;
        $order->address = Geolocation::address($lat, $lon);
        if($order->save()){
            // запуск процедури розсилки пропозицій
            return Propose::sendPropose($order);
        }else{
            Log::error('addOrder', 'order.log', false);
            Log::error($order->errors, 'order.log');
        }
        return false;
    }

    // якзо водій має активне замовлення, то протягом 10 хв не дозволяти йому брати інші
    public static function forbiddenAccept($id_driver)
    {
        if(self::find()
            ->where(['id_driver' => $id_driver])
            ->andWhere(['status' => Order::ISSUED])
            ->andWhere('create_dt > (NOW() - INTERVAL 10 MINUTE)')
            ->exists()){
            return true;
        }
        return false;
    }
}
