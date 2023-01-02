<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Breadcrumbs;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->registerJsFile('/js/user.js', ['depends' => 'app\assets\AppAsset', 'position'=> \yii\web\View::POS_END]);

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Користувачі', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">
    <?= Breadcrumbs::widget([
        'homeLink' => ['label' => 'Головна', 'url' => '/'],
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ]);
    ?>
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редагувати', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>
    <div class="btn btn-warning" onclick="reLogin(<?= $model->id ?>);">Зареєструватись під цим користувачем</div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
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
            'avatar',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
