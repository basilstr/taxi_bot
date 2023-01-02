<?php
/*
    Мини функции для дебага
 */

function l(... $arrs)
{
    foreach ($arrs as $arr) {
        if (!empty($arr)) {
            file_put_contents(__DIR__ . '/res.log', print_r($arr, true) . "\n", FILE_APPEND);
        }else{
            file_put_contents(__DIR__ . '/res.log', "Пусто\n", FILE_APPEND);
        }
    }
}

function l_sql($sql)
{
    l($sql->createCommand()->getRawSql());
}

function p($arr, $stop = false)
{
    echo "<style>body { background: #f4f4f4;}</style>";
    if (!empty($arr)) {
        print_r('<pre>' . print_r($arr, true) . '</pre>');
        if ($stop) die;
    } else {
        e('Пусто.');
    }
}


function g($msg = '', $n = false)
{
    e('good ' . $msg, $n);
}

function f($msg = '', $n = false)
{
    e('false ' . $msg, $n);
}

function non()
{
    e('non empty');
}

function br($n = 1)
{
    for ($i = 0; $i < $n; $i++) echo '<br>';
}

function e($str, $sn = false, $hr = false)
{
    echo $str;
    if (!$sn) br();
    else echo "\n";
    if ($hr) hr();
}

function ed($str, $sn = false, $hr = false)
{
    echo $str;
    if (!$sn) br();
    else echo "\n";
    if ($hr) hr();
    die;
}

function hr($n = false)
{
    e("--------------- \n", $n);
}

function memory()
{
    $size = memory_get_usage();
    $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
    $mem = @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    e($mem);
}

?>