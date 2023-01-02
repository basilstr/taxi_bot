<?php


namespace app\models;


class Log
{

    public static $foo;

    public static function add($message = '', $fileName = 'my_log.log', $end = true)
    {
        $dir = \Yii::getAlias("@app/runtime/logs/$fileName");
        $m = '>>  ' . date('H:i:s d-m-Y') . "\n" . $message . "\n";
        self::$foo .= $m;
        if ($end) {
            $m .= "-------------------------------\n";
        }

        if ($dir = self::getFilename($dir)) {
            @file_put_contents($dir, $m, FILE_APPEND);
        }

        return false;
    }

    public static function getFilename($pathFIle)
    {
        if (!file_exists($pathFIle)) {
            return $pathFIle;
        }

        $origin = $pathFIle;

        // создать другой файл, если предыдущий больше 10 мб
        for ($i = 1; $i <= 10; $i++) {
            $pathFIle = $origin . '_' . $i;
            if (file_exists($pathFIle)) { // 10 mb
                if (filesize($pathFIle) < (1024 * 1024 * 10)) {
                    return $pathFIle;
                }
            } else {
                return $pathFIle;
            }
        }

        return false;
    }

    public static function error($errorObject, $fileName, $end = true)
    {
        self::add(print_r($errorObject, true), $fileName, $end);
    }

}