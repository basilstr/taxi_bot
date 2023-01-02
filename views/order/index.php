<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Breadcrumbs;
use app\models\Order;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Замовлення';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <?= Breadcrumbs::widget([
        'homeLink' => ['label' => 'Головна', 'url' => '/'],
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ]);
    ?>
    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'id_client',
                'value' => function ($model) {
                    if($model->driver) {
                        $client = '<h4 class="text-primary">' .
                            Html::img($model->client->getAvatar(), ['style' => 'border-radius : 50px;', 'width' => '50', 'height' => '50'])
                            . ' ' .
                            Html::a($model->client->name, ['/bot-users/chat', 'id' => $model->client->id], ['target' => '_blank']) . '</h4>';
                        return $client;
                    }else{
                        return '-';
                    }
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'id_driver',
                'value' => function ($model) {
                    if($model->driver) {
                        $driver = '<h4 class="text-primary">' .
                            Html::img($model->driver->getAvatar(), ['style' => 'border-radius : 50px;', 'width' => '50', 'height' => '50'])
                            . ' ' .
                            Html::a($model->driver->name, ['/bot-users/chat', 'id' => $model->driver->id], ['target' => '_blank']) . '</h4>';
                        return $driver;
                    }else{
                        return '-';
                    }
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return Order::listStatuses()[$model->status];
                },
                'format' => 'raw',
            ],
            'address:ntext',
            [
                'attribute' => 'coordinates',
                'label' => 'Координати',
                'value' => function ($model) {
                    $url = "https://www.google.com/maps/place/{$model->lat}+{$model->lon}/@{$model->lat},{$model->lon},17z";
                    return Html::a('на карті', $url, ['class' => 'btn btn-success', 'target' => '_blank']);
                },
                'format' => 'raw',
            ],
            'create_dt',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {delete}',
            ],
        ],
    ]); ?>


</div>
