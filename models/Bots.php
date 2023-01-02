<?php

namespace app\models;

use Yii;
use app\models\viberApi\ViberClient;
use yii\helpers\Url;

/**
 * This is the model class for table "bots".
 *
 * @property int $id
 * @property string $token токен для бота
 * @property string $type тип бота viber | telegram
 * @property int $webhook установлен ли webhook для бота
 * @property string $name имя бота
 * @property string $url url на подписку
 * @property string $created_at
 */
class Bots extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bots';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['webhook'], 'integer'],
            [['created_at'], 'safe'],
            [['token', 'type', 'name', 'url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'token' => 'Токен',
            'type' => 'Тип',
            'webhook' => 'Зворотній зв\'язок',
            'name' => 'Им\'я',
            'url' => 'Url (повне значення)',
            'created_at' => 'Created At',
        ];
    }

    public static function typeBotsList()
    {
        return [
            'viber' => 'viber',
            'telegram' => 'telegram'
        ];
    }

    public static function setWebhook($id)
    {
        $bot = self::findOne($id);
        if(empty($bot)) return false;

        if($bot->type == 'viber'){
            $url = Url::toRoute('/bot/index/?index=' . $id, 'https');
            $client = new ViberClient(['token' => $bot->token]);

            try {
                $result = $client->deleteWebhook();
                $bot->webhook = 0;
            } catch (\Exception $e) {
                Log::error('deleteWebhook: ', 'bots.log', false);
                Log::error($e->getMessage(), 'bots.log');
            }

            try {
                $result = $client->setWebhook($url);
                if($result->isError()) {
                    Log::error('setWebhook: ', 'bots.log', false);
                    Log::error($result->getError(), 'bots.log');
                }else{
                    $bot->webhook = 1;
                }
            } catch (\Exception $e) {
                $bot->webhook = 0;
                Log::error('setWebhook: ', 'bots.log', false);
                Log::error($e->getMessage(), 'bots.log');
            }
        }
        $bot->save();
        return true;
    }

    public static function listViberBots()
    {
        $list = [];
        $listBots = self::find()->where(['type'=>'viber'])->all();
        foreach($listBots as $el){
            $list[$el->id] = $el->name.' ['.$el->url.']';
        }
        return $list;
    }
}
