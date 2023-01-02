<?php

namespace app\models\Scenario;

use app\models\BotUsers;

class BotDialog
{
    /**
     * весь диалог
     */
    public $currentDialog;
    public $error;

    public function __construct($currentDialog)
    {
        $this->currentDialog = $currentDialog;
    }

    public function validate($message)
    {
        $this->error = '';
        if (empty($message)) return true;
        // виводим первый вопрос в диалоге, где еще нет ответа
        foreach ($this->currentDialog['questions'] as $key => $currentDialog) {
            if (!empty($currentDialog['answer'])) continue;

            if ($currentDialog['type'] == 'int') {
                if (!ctype_digit($message)) {
                    $this->error = \Yii::t('app', 'Повинні бути тільки цифри');
                    return false;
                }
            }

            if ($currentDialog['type'] == 'date') {
                $date = explode('.', $message);
                if (!isset($date[0])) {
                    $this->error = \Yii::t('app', 'не вказаний день');
                    return false;
                }
                if (!isset($date[1])) {
                    $this->error = \Yii::t('app', 'не вказаний місяць');
                    return false;
                }
                if (!isset($date[2])) {
                    $this->error = \Yii::t('app', 'не вказаний рік');
                    return false;
                }
                if (!checkdate($date[1], $date[0], $date[2])) {
                    $this->error = \Yii::t('app', 'ви вказали помилкову дату');
                    return false;
                }
            }

            if ($currentDialog['type'] == 'time') {
                $date = explode(':', $message);
                if (!isset($date[0])) {
                    $this->error = \Yii::t('app', 'не вказані години');
                    return false;
                }
                if (!isset($date[1])) {
                    $message .= ':00';
                }
                if (strtotime($message) === false) {
                    $this->error = \Yii::t('app', 'ви вказали помилковий час');
                    return false;
                }
            }

            if ($currentDialog['type'] == 'phone') {
                if (!ctype_digit($message)) {
                    $this->error = \Yii::t('app', 'повинні бкти тільки цифри');
                    return false;
                }

                if (strlen($message) != 12) {
                    $this->error = \Yii::t('app', 'номер телефону введіть в форматі 380671234578');
                    return false;
                }
                if (substr($message, 0, 3) != '380') {
                    $this->error = \Yii::t('app', 'номер телефону повинен починатись з 380');
                    return false;
                }
            }

            // добавляем ответ, чтобы currentDialog можно было сохранить как обновленный результат
            $this->currentDialog['questions'][$key]['answer'] = $message;
            break;
        }
        return true;
    }

    /**
     * Получить значение поля
     * @param string $field
     * @return string
     */
    public function getAttribute($field)
    {
        foreach ($this->currentDialog['questions'] as $key => $currentDialog) {
            if ($currentDialog['field'] == $field) {
                return $currentDialog['answer'];
            }
        }
        return '';
    }

    /**
     * Установить значение поля
     * @param string $field
     * @return string
     */
    public function setAttribute($field, $value)
    {
        foreach ($this->currentDialog['questions'] as $key => $currentDialog) {
            if ($currentDialog['field'] == $field) {
                $this->currentDialog['questions'][$key]['answer'] = $value;
            }
        }
        return;
    }

    /**
     * действия после всех ответов
     *
     * @param BotUsers $botUser
     * @return string
     */
    public function endDialog($botUser)
    {
        if ($this->currentDialog['name'] == 'driver-car') {
            $res = $this->endDriverCar($botUser);
            return $res;
        }
        return '';
    }

    /**
     * @param BotUsers $botUser
     * @param $id_user
     * @return string
     */
    public function endDriverCar($botUser)
    {
        $botUser->parameters['driver']['brand'] = $this->getAttribute('brand');
        $botUser->parameters['driver']['number'] = $this->getAttribute('number');

        if ($botUser->save()) {
            return \Yii::t('app', 'Дані збережені');
        }
        return \Yii::t('app', 'Помилка при збереженні даних. Зверніться до адміністратора системи');
    }

    /**
     * 'question' -> вопрос, который будет задан пользователю в чате бота
     * 'field'    -> наименование поля, поля с именами [nameClient, nameUser, cityClient, cityUser] будут заполнены автоматически, если такой пользователь или контрагент существует в СРМ
     * 'type'     -> тип поля для проверки валидности введенных данных [phone, string, int]
     * 'answer'   -> ответ, который дал пользователь в чате бота
     *
     * @return array
     */

    public static function driverCar()
    {
        return [
            'name' => 'driver-car',
            'questions' => [
                ['question' => \Yii::t('app', 'Вкажіть марку автомобіля'), 'field' => 'brand', 'type' => 'string', 'answer' => ''],
                ['question' => \Yii::t('app', 'Вкажіть номерний знак автомобіля'), 'field' => 'number', 'type' => 'string', 'answer' => ''],
            ]
        ];
    }

}