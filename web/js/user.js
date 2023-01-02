function reLogin(id_user) {
    $.ajax({
        url: '/user/re-login/',
        type: 'post',
        data: {id_user: id_user, _csrf: yii.getCsrfToken()},
        success: function (data) {
            document.location.href = '/index.php';
        },
        error: function (data) {
            console.log('Ошибка setMessage: ' + data.status + ' ' + data.statusText);
        }
    });
}
$('document').ready(function () {
    $('#dt_ban').datepicker({autoclose: true, todayHighlight: true, format: "yyyy-mm-dd"});
});
