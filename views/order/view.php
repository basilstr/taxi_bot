<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Breadcrumbs;
use app\models\Order;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Замовлення', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-view">

    <?= Breadcrumbs::widget([
        'homeLink' => ['label' => 'Головна', 'url' => '/'],
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ]);
    ?>
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Видалити', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Ви впевненні в видаленні?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
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
            'lat',
            'lon',
            'create_dt',
        ],
    ]) ?>

</div>
