<?php
use app\models\User;
use yii\helpers\Html;
?>
<!-- BEGIN HEADER-->
<header id="header" >
    <div class="headerbar">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="headerbar-left">
            <ul class="header-nav header-nav-options">
                <li class="header-nav-brand" >
                    <div class="brand-holder">
                        <a href="#">
                            <span class="text-lg text-bold text-primary">ПАНЕЛЬ</span>
                        </a>
                    </div>
                </li>
                <li>
                    <a class="btn btn-icon-toggle menubar-toggle" data-toggle="menubar" href="javascript:void(0);">
                        <i class="fa fa-bars"></i>
                    </a>
                </li>
            </ul>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->

        <div class="headerbar-right">
            <ul class="header-nav header-nav-profile">
                <li class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle ink-reaction" data-toggle="dropdown">
                        <img src="<?= '/uploads/avatars/' . User::findIdentity(\Yii::$app->user->id)->avatar ?>" alt="" />
								<span class="profile-info">
                                    <?=
                                    $user = User::findIdentity(\Yii::$app->user->id)->name;
                                    ?>
									<small>користувач</small>
								</span>
                    </a>
                    <ul class="dropdown-menu animation-dock">
                        <li class="dropdown-header">основне</li>
                        <li><a href="/account">Профіль</a></li>
                        <li class="divider"></li>
                        <li><a href="/site/logout"><i class="fa fa-fw fa-power-off text-danger"></i> Вихід</a></li>
                    </ul><!--end .dropdown-menu -->
                </li><!--end .dropdown -->
            </ul><!--end .header-nav-profile -->
        </div>
    </div>
</header>
<!-- END HEADER-->