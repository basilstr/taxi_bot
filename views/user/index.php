<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Breadcrumbs;
use app\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Користувачі';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
    <?= Breadcrumbs::widget([
        'homeLink' => ['label' => 'Головна', 'url' => '/'],
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ]);
    ?>
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Створити користувача', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'username',
            [
                'attribute' => 'role',
                'value' => function ($model) {
                    $listRole = User::listRole();
                    if(isset($listRole[$model->role])) return $listRole[$model->role];
                    return '<span class="text-danger">Роль не визначена</span>';
                },
                'format' => 'html',
            ],
            //'auth_key',
            //'password_hash',
            //'password_reset_token',
            'name',
            'email:email',
            'phone',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    if($model->status == 1) return '<span class="text-success">Активний</span>';
                    if($model->status == 2) return '<span class="text-danger">Відключений</span>';
                    return '<span class="text-danger">Не визначений</span>';
                },
                'format' => 'html',
            ],
            //'avatar',
            //'telegram_code',
            //'telegram_dt_create',
            //'telegram_chat_id',
            //'created_at',
            'updated_at',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view} {update}'],
        ],
    ]); ?>


</div>
