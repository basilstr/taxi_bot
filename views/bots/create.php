<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\bot\models\Bots */

$this->title = 'Создать бота';
$this->params['breadcrumbs'][] = ['label' => 'Боты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bots-create">
    <?= \yii\widgets\Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : []]); ?>
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
