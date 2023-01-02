<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Breadcrumbs;
use app\models\BotUsers;

/* @var $this yii\web\View */
/* @var $model app\models\BotUsers */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Користувачі ботів', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="bot-users-view">
    <?= Breadcrumbs::widget([
        'homeLink' => ['label' => 'Головна', 'url' => '/'],
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ]);
    ?>
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редагувати', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Видалити', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Ви впевнені в видаленні?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'bot',
                'label' => 'Бот',
                'value' => function ($model) {
                    return $model->bot->name;
                },
                'format' => 'html',
            ],
            'chat_id',
            [
                'attribute' => 'avatar',
                'value' => function ($model) {
                    return $model->avatar ? Html::img($model->avatar, ['width' => '70', 'height' => '70']) : '-';
                },
                'format' => 'raw',
            ],
            'phone',
            'name',
            [
                'attribute' => 'is_subscribe',
                'value' => function ($model) {
                    if($model->is_subscribe == 1) return '<span class="text-success">Підписаний</span>';
                    if($model->is_subscribe == 0) return '<span class="text-danger">Непідписаний</span>';
                    return '<span class="text-danger">Не визначений</span>';
                },
                'format' => 'html',
            ],
            //'current_type',
            [
                'attribute' => 'current_type',
                'value' => function ($model) {
                    if($model->current_type == 1) return 'Водій';
                    if($model->current_type == 2) return 'Пасажир';
                    return '<span class="text-danger">Не визначений</span>';
                },
                'format' => 'html',
            ],
            [
                'attribute' => 'brand',
                'label' => 'Авто',
                'value' => function ($model) {
                    if(isset($model->parameters['driver']['brand'])) return $model->parameters['driver']['brand'];
                    return '-';
                },
                'format' => 'html',
            ],
            [
                'attribute' => 'number',
                'label' => 'Номер',
                'value' => function ($model) {
                    if(isset($model->parameters['driver']['number'])) return $model->parameters['driver']['number'];
                    return '-';
                },
                'format' => 'html',
            ],
            'language',
            [
                'attribute' => 'number',
                'label' => 'Номер',
                'value' => function ($model) {
                    if(isset($model->parameters['driver']['number'])) return $model->parameters['driver']['number'];
                    return '-';
                },
                'format' => 'html',
            ],
            [
                'attribute' => 'dt_subscribe',
                'format' => ['date', 'php:Y-m-d']
            ],
            [
                'attribute' => 'dt_last_action',
                'format' => ['date', 'php:Y-m-d']
            ],
            [
                'attribute' => 'dt_ban',
                'format' => ['date', 'php:Y-m-d']
            ],
            [
                'attribute' => 'admin_msg',
                'value' => function ($model) {
                    return BotUsers::typeAdminMsg()[$model->admin_msg];
                },
                'format' => 'html',
            ],
        ],
    ]) ?>

</div>
