<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\BotUsers;

/* @var $this yii\web\View */
/* @var $model app\models\BotUsers */
/* @var $form yii\widgets\ActiveForm */

$this->registerJsFile('/js/user.js', ['depends' => 'app\assets\AppAsset', 'position'=> \yii\web\View::POS_END]);

?>

<div class="bot-users-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_bot')->textInput() ?>

    <?= $form->field($model, 'chat_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_subscribe')->dropDownList(BotUsers::typeSubscribe()) ?>

    <?= $form->field($model, 'current_type')->dropDownList(BotUsers::typeUser()) ?>

    <?= $form->field($model, 'current_dialog')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'lat')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lon')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dt_coordinate')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'language')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'avatar')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dt_subscribe')->textInput() ?>

    <?= $form->field($model, 'dt_last_action')->textInput() ?>

        <div class="input-group date" id="dt_ban">
            <div class="input-group-content">
                <?= $form->field($model, 'dt_ban')->textInput() ?>
            </div>
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        </div>

    <?= $form->field($model, 'admin_msg')->dropDownList(BotUsers::typeAdminMsg()) ?>

    <div class="form-group">
        <?= Html::submitButton('Зберегти', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
