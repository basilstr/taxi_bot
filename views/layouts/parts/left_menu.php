<?php
use app\models\User;
?>
<!-- BEGIN MENUBAR-->
<div id="menubar" class="menubar-inverse ">
    <div class="menubar-fixed-panel">
        <div>
            <a class="btn btn-icon-toggle btn-default menubar-toggle" data-toggle="menubar" href="javascript:void(0);">
                <i class="fa fa-bars"></i>
            </a>
        </div>
        <div class="expanded">
            <a href="#">
                <span class="text-lg text-bold text-primary ">ADMIN PANEL</span>
            </a>
        </div>
    </div>
    <div class="menubar-scroll-panel">

        <!-- BEGIN MAIN MENU -->
        <ul id="main-menu" class="gui-controls">

            <!-- BEGIN DASHBOARD -->
            <li>
                <a href="#" class="active">
                    <div class="gui-icon"><i class="md md-home"></i></div>
                    <span class="title">ПАНЕЛЬ</span>
                </a>
            </li><!--end /menu-li -->
            <!-- END DASHBOARD -->

            <!-- BEGIN UI -->
            <li class="gui-folder">
                <a>
                    <div class="gui-icon"><i class="md md-settings"></i></div>
                    <span class="title">Налаштування</span>
                </a>
                <!--start submenu -->
                <ul>
                    <li><a href="/account" ><span class="title">Профіль</span></a></li>
                    <?php if(User::isAdmin()) : ?>
                        <li><a href="/user" ><span class="title">Користувачі системи</span></a></li>
                        <li><a href="/bots" ><span class="title">Чат боти</span></a></li>
                    <?php endif; ?>
                </ul><!--end /submenu -->
            </li><!--end /menu-li -->

            <!-- BEGIN TABLES -->
            <li class="gui-folder">
                <a>
                    <div class="gui-icon"><i class="glyphicon glyphicon-user"></i></div>
                    <span class="title">Переписка</span>
                </a>
                <ul>
                    <li><a href="/bot-users" ><span class="title">Користувачі ботів</span></a></li>
                    <li><a href="/order" ><span class="title">Замовлення</span></a></li>
                </ul><!--end /submenu -->
            </li><!--end /menu-li -->

        </ul><!--end .main-menu -->
        <!-- END MAIN MENU -->

        <div class="menubar-foot-panel">
            <small class="no-linebreak hidden-folded">
                <span class="opacity-75">Copyright &copy; 2020</span> <strong>CityGroup</strong>
            </small>
        </div>
    </div><!--end .menubar-scroll-panel-->
</div><!--end #menubar-->
<!-- END MENUBAR -->