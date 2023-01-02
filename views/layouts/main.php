<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <!-- BEGIN META -->
    <meta name="keywords" content="your,keywords">
    <meta name="description" content="Short explanation about this website">
    <!-- END META -->

    <!-- BEGIN STYLESHEETS -->
    <link href='http://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>

    <!-- END STYLESHEETS -->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="../../assets/js/libs/utils/html5shiv.js?1403934957"></script>
    <script type="text/javascript" src="../../assets/js/libs/utils/respond.min.js?1403934956"></script>
    <![endif]-->
</head>
<body class="menubar-hoverable header-fixed ">
<?php $this->beginBody() ?>

<!--Header start-->
<?php if(\Yii::$app->user->isGuest == false) : ?>
    <?= $this->render('parts/top_header') ?>
<?php else: ?>
    <?php if(\Yii::$app->request->url != '/') : ?>
        <?= $this->render('/site/start') ?>
    <?php else: ?>
        <?= $this->render('/site/lending') ?>
    <?php endif ?>
<?php endif ?>
<!--Header end-->
<?php if(\Yii::$app->request->url != '/' || \Yii::$app->user->isGuest == false) : ?>
    <div id="base">
        <div class="offcanvas"></div>
        <div id="content">
            <section>
                <div class="section-body">
                    <?= $this->render('//layouts/_messages') ?>
                    <div class="row">
                        <?= $content ?>
                    </div>
                </div>
            </section>
        </div>
        <?= \Yii::$app->user->isGuest == false ? $this->render('parts/left_menu') : ''; ?>
    </div>
<?php endif ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
