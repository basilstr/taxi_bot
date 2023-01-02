<?php
foreach ($messages as $message) {
    if ($message->id_sender == $botUser->id) {
        echo $this->render('messages_receiver', [
            'message' => $message,
        ]);
    } else {
        echo $this->render('messages_sender', [
            'message' => $message,
        ]);
    }
}

