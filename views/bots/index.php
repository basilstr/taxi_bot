<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Боти';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bots-index">
    <?= \yii\widgets\Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : []]); ?>
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Створити бота', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'token',
            'type',
            [
                'attribute' => 'webhook',
                'value' => function ($model) {
                    if($model->webhook) return  'встановлений';
                    return  'не встановлений';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'url',
                'value' => function ($model) {
                    return Html::a($model->url, $model->url, ['target' => '_blank', 'class' => 'badge badge-primary']);
                },
                'format' => 'raw',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete} {connect}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                            ['/bots/view', 'id' => $model->id]);
                    },

                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                            ['/bots/update', 'id' => $model->id]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                            ['/bots/delete', 'id' => $model->id],
                            [
                                'data-confirm' => 'Удалить ?',
                                'data-method' => 'post',
                                'data-pjax' => '0',
                            ]
                        );
                    },
                    'connect' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-refresh" title="Подключиться к боту"></span>',
                            ['/bots/connect', 'id' => $model->id]);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
