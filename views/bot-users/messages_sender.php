<?php
use app\models\BotMessages;
/** @var BotMessages $message */
?>
<div class="chatMessageType1">
    <div class="chatMessageTextAutor"><?= $message->userSender ? $message->userSender->name : 'taxi-M' ?> :</div>
    <div class="chatMessageText"><?= $message->text ?></div>
    <div class="chatMessageBottom">
        <?php if ($message->readed) : ?>
            <span class="glyphicon glyphicon-ok" style="color:gray; padding-right: 10px;"></span>
        <?php endif; ?>
        <div class="chatMessageBottomDate"><?= date('d.m.Y - H:i', strtotime($message->create_dt)); ?></div>
    </div>
</div>