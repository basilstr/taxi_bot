<?php
use yii\helpers\Html;
use app\models\BotUsers;
$this->registerJsFile('/js/bot.js', ['depends' => 'app\assets\AppAsset', 'position'=> \yii\web\View::POS_END]);
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="text-primary"><?= Html::img($botUser->getAvatar(), ['style' => 'border-radius : 50px;', 'width' => '50', 'height' => '50']) ?> <?= $botUser->name ?></h1>
    </div>
    <!--end .col -->
    <div class="col-sm-3">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-pills nav-stacked nav-transparent">
                    <li>Телефон<span class="badge pull-right"><?= $botUser->phone ?></span></li>
                    <li>Підписаний<span class="badge pull-right <?=  $botUser->is_subscribe ? 'bgGreen' : 'bgRed' ?>"><?= $botUser->is_subscribe ? 'ТАК' : 'НІ' ?></span>
                    </li>
                    <li>Тип клієнта<span
                            class="badge pull-right <?=  $botUser->current_type == null || $botUser->current_type == 0 ? 'bgRed' : '' ?><?=  $botUser->current_type == 1 ? 'bgGreen' : '' ?><?=  $botUser->current_type == 2 ? 'bgBlue' : '' ?>"><?= BotUsers::getTypeUser($botUser->current_type) ?></span></li>
                    <li>Активність<span class="badge pull-right"><?= $botUser->dt_last_action ?></span></li>
                    <li>Бан<span class="badge pull-right"><?= $botUser->dt_ban ?></span></li>
                </ul>
            </div>
        </div>
    </div>
    <!--end .col -->
    <div class="col-sm-9">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                <?php echo $this->render('send_message', [
                    'botUser' => $botUser,
                ]); ?>
            </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="chatMessagesContainer">
                        <?php echo $this->render('messages', [
                            'botUser' => $botUser,
                            'messages' => $messages,
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
