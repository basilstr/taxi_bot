<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BotUserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bot-users-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_bot') ?>

    <?= $form->field($model, 'chat_id') ?>

    <?= $form->field($model, 'phone') ?>

    <?= $form->field($model, 'is_subscribe') ?>

    <?php // echo $form->field($model, 'current_type') ?>

    <?php // echo $form->field($model, 'current_menu') ?>

    <?php // echo $form->field($model, 'current_dialog') ?>

    <?php // echo $form->field($model, 'params') ?>

    <?php // echo $form->field($model, 'lat') ?>

    <?php // echo $form->field($model, 'lon') ?>

    <?php // echo $form->field($model, 'dt_coordinate') ?>

    <?php // echo $form->field($model, 'name') ?>

    <?php // echo $form->field($model, 'language') ?>

    <?php // echo $form->field($model, 'select_language') ?>

    <?php // echo $form->field($model, 'avatar') ?>

    <?php // echo $form->field($model, 'dt_subscribe') ?>

    <?php // echo $form->field($model, 'dt_last_action') ?>

    <?php // echo $form->field($model, 'dt_ban') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
