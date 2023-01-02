<?php
namespace app\models\Scenario;

use app\models\viberApi\Api\Keyboard;
use app\models\viberApi\Api\Keyboard\Button;
use yii\helpers\Url;


class BotButtonMenu
{

    public static function menuStartButton()
    {
        return (new Keyboard())
            ->setInputFieldState('hidden')
            ->setBgColor('#ffffff')
            ->setButtons([
                (new Button())
                    ->setSilent(true)
                    ->setColumns(2)
                    ->setBgColor('#328fcd')
                    ->setTextSize('large')
                    ->setTextHAlign('center')
                    ->setActionType('reply')
                    ->setActionBody('/btn-menu-1')
                    ->setImage('https://taxi-m.pp.ua/img/btn_2.png')
                    ->setText('<b><font color="#ffffff">' . \Yii::t('app', 'Я водій') . '</font></b>'),

                (new Button())
                    ->setSilent(true)
                    ->setColumns(2)
                    ->setBgColor('#328fcd')
                    ->setTextSize('large')
                    ->setTextHAlign('center')
                    ->setActionType('reply')
                    ->setActionBody('/btn-menu-2')
                    ->setImage('https://taxi-m.pp.ua/img/btn_2.png')
                    ->setText('<b><font color="#ffffff">' . \Yii::t('app', 'Я клієнт') . '</font></b>'),

                (new Button())
                    ->setSilent(true)
                    ->setColumns(2)
                    ->setBgColor('#328fcd')
                    ->setTextSize('large')
                    ->setTextHAlign('center')
                    ->setActionType('reply')
                    ->setActionBody('/btn-menu-4')
                    ->setImage('https://taxi-m.pp.ua/img/btn_2.png')
                    ->setText('<b><font color="#ffffff">' . \Yii::t('app', 'Інструкція') . '</font></b>'),
            ]);
    }

    public static function menuDriverButton()
    {
        return (new Keyboard())
            ->setInputFieldState('hidden')
            ->setBgColor('#ffffff')
            ->setButtons([
                (new Button())
                    ->setSilent(true)
                    ->setColumns(2)
                    ->setBgColor('#328fcd')
                    ->setTextSize('large')
                    ->setTextHAlign('center')
                    ->setActionType('reply')
                    ->setActionBody('/btn-menu-3')
                    ->setImage('https://taxi-m.pp.ua/img/btn_2.png')
                    ->setText('<b><font color="#ffffff">' . \Yii::t('app', 'змінити авто') . '</font></b>'),

                (new Button())
                    ->setSilent(true)
                    ->setColumns(2)
                    ->setBgColor('#328fcd')
                    ->setTextSize('large')
                    ->setTextHAlign('center')
                    ->setActionType('location-picker')
                    ->setActionBody('location')
                    ->setTextSize('regular')
                    ->setImage('https://taxi-m.pp.ua/img/btn_2.png')
                    ->setText('<b><font color="#ffffff">' . \Yii::t('app', 'координати') . '</font></b>'),

                (new Button())
                    ->setSilent(true)
                    ->setColumns(2)
                    ->setBgColor('#328fcd')
                    ->setTextSize('large')
                    ->setTextHAlign('center')
                    ->setActionType('reply')
                    ->setActionBody('/btn-menu-0')
                    ->setImage('https://taxi-m.pp.ua/img/btn_2.png')
                    ->setText('<b><font color="#ffffff">' . \Yii::t('app', 'меню') . '</font></b>'),
            ]);
    }

    public static function menuClientButton()
    {
        return (new Keyboard())
            ->setInputFieldState('hidden')
            ->setBgColor('#ffffff')
            ->setButtons([
                (new Button())
                    ->setSilent(true)
                    ->setColumns(4)
                    ->setBgColor('#328fcd')
                    ->setTextSize('large')
                    ->setTextHAlign('center')
                    ->setActionType('location-picker')
                    ->setActionBody('location')
                    ->setImage('https://taxi-m.pp.ua/img/btn_4.png')
                    ->setText('<b><font color="#ffffff">' . \Yii::t('app', 'замовити авто') . '</font></b>'),

                (new Button())
                    ->setSilent(true)
                    ->setColumns(2)
                    ->setBgColor('#328fcd')
                    ->setTextSize('large')
                    ->setTextHAlign('center')
                    ->setActionType('reply')
                    ->setActionBody('/btn-menu-0')
                    ->setImage('https://taxi-m.pp.ua/img/btn_2.png')
                    ->setText('<b><font color="#ffffff">' . \Yii::t('app', 'меню') . '</font></b>')
            ]);
    }

    public static function MenuInlineButton($listMenu)
    {
        $buttons = [];
        foreach ($listMenu as $elMenu) {
            $buttons[] = (new Button())
                ->setSilent(true)
                ->setColumns(6)
                ->setRows(1)
                ->setBgColor('#328fcd')
                ->setTextHAlign('center')
                ->setActionType('reply')
                ->setActionBody($elMenu['btn'])
                ->setText('<b><font color="#ffffff">' . $elMenu['text'] . '</font></b>');
        }
        return $buttons;
    }
}