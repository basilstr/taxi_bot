<?php
use yii\bootstrap\Alert;
/* 
 * Выведем все сообщения в цикле.
 */
echo '<div class="fullscreen">';

foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
    //Проверим, существует ли сессия по ключу
    if(Yii::$app->getSession()->hasFlash($key)){
        echo Alert::widget([
            'options' => [
//Тут задаем css класс для оформления текущего блока сообщения
//В bootstrap 3 alerts есть такие классы - 'success', 'info', 'warning', 'danger'
//В данном случае $key - это имя сообщения, а содерание - $message
//Если имя сообщения не соответствует четырем названиям классов в alerts стилях, то
//создаем один из классов как стиль по умолчанию.
                'class' => (in_array($key, ['success', 'info', 'warning', 'danger']) ? 'alert-' . $key : 'alert-info'),
            ],
            'body' => $message,
        ]);
    }
}

echo '</div>';

//Добавим стилей, чтобы сообщения были фиксированы на экране, чтобы точно были замечены //пользователем.
$css = <<<CSS
div.fullscreen {
    position: fixed;
    z-index: 10000;
    width: 100%;
    height: auto;
    padding: 0 2%;
    top: 3px;
    left: 0;
}        
        
CSS;

//Регистрируем стили в приложении
Yii::$app->getView()->registerCss($css);