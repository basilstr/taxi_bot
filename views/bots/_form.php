<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Bots;

/* @var $this yii\web\View */
/* @var $model app\models\Bots */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bots-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList(Bots::typeBotsList()) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
