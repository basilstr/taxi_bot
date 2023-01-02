<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BotUsers */

$this->title = 'Create Bot Users';
$this->params['breadcrumbs'][] = ['label' => 'Bot Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bot-users-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
