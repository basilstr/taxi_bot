<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BotUserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Користувачі ботів';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bot-users-index">

    <?= Breadcrumbs::widget([
        'homeLink' => ['label' => 'Головна', 'url' => '/'],
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ]);
    ?>
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Створити нового користувача', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'bot',
                'label' => 'Бот',
                'value' => function ($model) {
                    return $model->bot->name;
                },
                'format' => 'html',
            ],
            //'chat_id',
            [
                'attribute' => 'avatar',
                'value' => function ($model) {
                    $client =
                        Html::img($model->getAvatar(), ['style'=>'border-radius : 50px;', 'width' => '50', 'height' => '50'])
                        .' '.
                        Html::a($model->name, ['/bot-users/chat', 'id' => $model->id], ['class' => 'text-primary', 'target' => '_blank']);
                    return $client;
                },
                'format' => 'raw',
            ],
            'phone',
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
                    $brand = isset($model->parameters['driver']['brand']) ? $model->parameters['driver']['brand'] : '';
                    $number = isset($model->parameters['driver']['number']) ? $model->parameters['driver']['number'] : '';
                    if(empty($brand) && empty($number)) return '-';
                    return $brand.'<br>'.$number;
                },
                'format' => 'html',
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
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete} {chat}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                            ['/bot-users/view', 'id' => $model->id]);
                    },

                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                            ['/bot-users/update', 'id' => $model->id]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                            ['/bot-users/delete', 'id' => $model->id],
                            [
                                'data-confirm' => 'Видалити ?',
                                'data-method' => 'post',
                                'data-pjax' => '0',
                            ]
                        );
                    },
                    'chat' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-list-alt"></span>',
                            ['/bot-users/chat', 'id' => $model->id]);
                    },
                ],
            ],
        ],
    ]); ?>


</div>
