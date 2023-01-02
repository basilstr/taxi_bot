<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/theme-default/bootstrap.css',
        'css/theme-default/libs/bootstrap-datepicker/datepicker3.css',
        'css/theme-default/materialadmin.css',
        'css/theme-default/font-awesome.min.css',
        'css/theme-default/material-design-iconic-font.min.css',
        'css/theme-default/libs/rickshaw/rickshaw.css',
        'css/theme-default/libs/morris/morris.core.css',
    ];
    public $js = [
        'js/libs/jquery/jquery-1.11.2.min.js',
        'js/libs/bootstrap/bootstrap.min.js',
        'js/libs/moment/moment.min.js',
        'js/libs/bootstrap-datepicker/bootstrap-datepicker.js',
        'js/libs/jquery/jquery-migrate-1.2.1.min.js',
        'js/libs/spin.js/spin.min.js',
        'js/libs/autosize/jquery.autosize.min.js',
        'js/libs/flot/jquery.flot.min.js',
        'js/libs/flot/jquery.flot.time.min.js',
        'js/libs/flot/jquery.flot.resize.min.js',
        'js/libs/flot/jquery.flot.orderBars.js',
        'js/libs/flot/jquery.flot.pie.js',
        'js/libs/flot/curvedLines.js',
        'js/libs/jquery-knob/jquery.knob.min.js',
        'js/libs/sparkline/jquery.sparkline.min.js',
        'js/libs/nanoscroller/jquery.nanoscroller.min.js',
        'js/libs/d3/d3.min.js',
        'js/libs/d3/d3.v3.js',
        'js/libs/rickshaw/rickshaw.min.js',
        'js/core/source/App.js',
        'js/core/source/AppNavigation.js',
        'js/core/source/AppOffcanvas.js',
        'js/core/source/AppCard.js',
        'js/core/source/AppForm.js',
        'js/core/source/AppNavSearch.js',
        'js/core/source/AppVendor.js',
        //'js/libs/toastr/toastr.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
