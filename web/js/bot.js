function sendMessage(id_user)
{
    var msg = $('#messate_text').val();
    $.ajax({
        url: '/bot/send-message/',
        type: 'post',
        data: {id_user: id_user, msg:msg, _csrf: yii.getCsrfToken()},
        success: function (data) {
            location.reload();
        },
        error: function (data) {
            console.log('Ошибка sendMessage: ' + data.status + ' ' + data.statusText);
        }
    });
}
