<!DOCTYPE html>
<html lang="ua">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1">
    <meta name="author" content="Іван Михавко (Ivan Tegos Mykhavko )">
    <meta name="keywords"
          content="<?php echo $keywords; ?>">

    <meta name="description"
          content="<?php echo $description; ?>">
    <title><?php echo $title; ?></title>

    <link href="http://fonts.googleapis.com/css?family=Roboto:300,400,700|"
          rel="stylesheet" type="text/css"/>
    <link href="/assets/fonts/font-awesome.min.css" rel="stylesheet" type="text/css"/>

    <link rel="stylesheet" href="/assets/css/easy-autocomplete.css"/>
    <link rel="stylesheet" href="/assets/css/easy-autocomplete.themes.css"/>
    <link rel="stylesheet" href="/assets/css/style.css"/>
    <link rel="stylesheet" href="/assets/css/add.css"/>
    <meta name="yandex-verification" content="b1ee326afc347f89"/>
    <meta name="yandex-verification" content="eb6d57b876ddf2e1"/>

    <link rel="canonical" href="<?php echo $canonical; ?>"/>
    <link rel="icon" href="/assets/images/icons/iwea.png"/>
</head>


<body>

<div class="site-content">
    <div class="site-header">
        <div class="container">
            <a href="/" class="branding">
                <img src="/assets/images/logo.png" title="Logo iWea" alt="Logo iWea" class="logo"/>

                <div class="logo-type">
                    <?php if(isset($is_home)){ ?>
                    <h1 class="site-title">iWEA</h1>
                    <?php }else{ ?>
                    <h2 class="site-title">iWEA</h2>
                    <?php } ?>

                    <h2 class="site-description">Веб-застосування для порівняння прогнозу погоди за різними сайтами
                    </h2>
                </div>
            </a>

            <div class="main-navigation">
                <button type="button" class="menu-toggle"><i class="fa fa-bars"></i></button>
                <ul class="menu">
                    <li class="menu-item current-menu-item"><a href="/">Головна</a></li>
                    <li class="menu-item"><a href="/page/info">Список джерел</a></li>
                    <li class="menu-item"><a href="/page/all">Погода з усіх джерел</a></li>
                    <li class="menu-item"><a href="/page/analytics">Аналітика</a></li>
                    <?php /* ?>
                    <?php if (!$user){ ?>
                    <li class="menu-item"><a href="/?action=auth_reg">Авторизація Реєстрація</a></li>
                    <?php }else{ ?>
                    <li class="menu-item">
                        <a href="/?action=account">
                            <?php echo $user['name']; ?><br/>
                            <?php echo $user['email']; ?>
                        </a>
                    </li>
                    <?php } ?>
                    <?php */ ?>
                </ul> <!-- .menu -->
            </div> <!-- .main-navigation -->

            <div class="mobile-navigation"></div>

        </div>
    </div>