<!DOCTYPE html>
<html lang="<?= substr(I18n::$lang, 0, 2); ?>-UA" xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?= substr(I18n::$lang, 0, 2); ?>-UA">
<head>
    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script>
        (adsbygoogle = window.adsbygoogle || []).push({
            google_ad_client: "ca-pub-8070131313284762",
            enable_page_level_ads: true
        });
    </script>

    <meta charset="utf-8">
    <title><?= $title . ($currentPage ? " - " . $currentPage . " страница" : '')?></title>
    <!--    <meta content="text/html;charset=utf-8" http-equiv="Content-Type">-->
    <!--    <meta content="utf-8" http-equiv="encoding">-->
    <meta name="description" content="<?= $description; ?>">
    <?php if(isset($next)): ?>
        <link rel="next" href="<?= $next; ?>">
    <?php endif; ?>
    <?php if(isset($prev)): ?>
        <link rel="prev" href="<?= $prev; ?>">
    <?php endif; ?>

    <?php if(isset($noindex)): ?>
        <meta name="robots" content="noindex, follow" />
    <?php endif; ?>

    <link href="<?= $canonical; ?>" rel="canonical">

<!--    <meta name="keywords" content="--><?//= $keywords; ?><!--">-->
    <meta name="yandex-verification" content="6bb0e9333c88c3cc" />
    <meta name="google-site-verification" content="5TjBx0W8WXQ9dquPrGpZx_P1Z16sFBjTKIIEORYzTec" />
    <meta name="google-site-verification" content="JbTa9isrE6X5nq5niE109-TsDLyUc8m90wAWaQzhK2E" />
    <meta name="author" content="<?= $author; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (Helper_Url::currentUrl() == Helper_Url::createUrl('importing-vehicle')): ?>
        <meta name="robots" content="noindex">
    <?php endif;?>

    <!-- Put this script tag to the  of your page -->
    <!--    <script type="text/javascript" src="http://vk.com/js/api/share.js?94" charset="windows-1251"></script>-->
    <!-- vk -->
<!--    --><?php //if ((isset($noindex) AND $noindex != 0) OR ($currentPage !== false)) { ?>
<!--        <meta name="robots" content="noindex, nofollow">-->
<!--    --><?php //} ?>

    <?php if (isset($open_graph)) { ?>
        <meta property="og:title" content="<?=$open_graph['title']?>" />
        <meta property="og:type" content="<?=$open_graph['type']?>" />
        <meta property="og:url" content="<?=$open_graph['url']?>" />
    <?php } ?>
    <link href='https://fonts.googleapis.com/css?family=Roboto:500,300,700,400italic,400' rel='stylesheet'
          type='text/css'>

    <link rel="icon" href="<?= URL::base(); ?>media/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="<?= URL::base(); ?>media/favicon.ico" type="image/x-icon">

    <?php foreach ($styles as $style) : ?>

        <link rel="stylesheet" href="<?= URL::base(); ?>media/css/<?= $style; ?>.css" />
    <?php endforeach; ?>

    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-24410081-1', 'auto');
        ga('send', 'pageview');

    </script>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-132749518-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-132749518-1');
    </script>
    <!-- Yandex.Metrika counter -->
    <script>
        (function (d, w, c) {
            (w[c] = w[c] || []).push(function() {
                try {
                    w.yaCounter11808505 = new Ya.Metrika({
                        id:11808505,
                        clickmap:true,
                        trackLinks:true,
                        accurateTrackBounce:true,
                        webvisor:true
                    });
                } catch(e) { }
            });

            var n = d.getElementsByTagName("script")[0],
                s = d.createElement("script"),
                f = function () { n.parentNode.insertBefore(s, n); };
            s.type = "text/javascript";
            s.async = true;
            s.src = "https://d31j93rd8oukbv.cloudfront.net/metrika/watch_ua.js";

            if (w.opera == "[object Opera]") {
                d.addEventListener("DOMContentLoaded", f, false);
            } else { f(); }
        })(document, window, "yandex_metrika_callbacks");
    </script>

</head>

<body>
<div class="">
    <div class="menu_fixed_top">
        <ul class="ul_menu_fixed_top">
        </ul>
    </div>
</div>
<div class="global-wrapper clearfix" id="global-wrapper">
    <div class="navbar-before mobile-hidden navbar-before-inverse">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <p class="navbar-before-sign">Интернет-магазин автозапчастей ULC</p>
                </div>
                <div class="col-md-8">
                    <ul class="nav navbar-nav navbar-right navbar-right-no-mar">
                        <li>
                            <span class="external-reference" data-link="<?= Helper_Url::createUrl('kak-zakazat'); ?>">Как заказать</span>
                        </li>
                        <li>
                            <span class="external-reference" data-link="<?= Helper_Url::createUrl('sposoby-oplaty-tovara'); ?>">Способы оплаты товара</span>
                        </li>
                        <li>
                            <span class="external-reference" data-link="<?= Helper_Url::createUrl('informaciya-o-dostavke'); ?>">Информация о доставке</span>
                        </li>
                        <li>
                            <span class="external-reference" data-link="<?= Helper_Url::createUrl("politika-vozvrata"); ?>">Политика возврата</span>
                        </li>
                        <li>
                            <span class="external-reference" data-link="<?= Helper_Url::createUrl("optovym-pokupatelyam"); ?>">Оптовым покупателям</span>
                        </li>
                        <li>
                            <span class="external-reference" data-link="<?= Helper_Url::createUrl("postavshchikam"); ?>">Поставщикам</span>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!--        Модальное окно на ввойти-->
    <div class="mfp-with-anim mfp-hide mfp-dialog clearfix" id="nav-login-dialog">
        <p class="widget-title none-fw pro-h"><i class="fa fa-sign-in"></i>&nbsp;Войти</p>
        <p>Введите логин и пароль, или зарегестрируйтесь</p>
        <hr/>
        <form action="<?= Helper_Url::createUrl('authorization/login'); ?>" method="POST" id="loginForm" autocomplete="off">
            <div class="form-group">
                <label>Номер телефона</label>
                <input class="form-control phone" placeholder="+38" type="text" name="phone" required/>
            </div>
            <div class="form-group">
                <label>Пароль</label>
                <input class="form-control" type="password" name="password" required/>
            </div>
            <div class="checkbox">
                <label>
                    <input class="i-check" type="checkbox"/>Запомнить</label>
            </div>
            <input class="btn btn-primary" type="submit" value="Войти"/>

            <div class="gap gap-small"></div>

            <ul class="list-inline">
                <li><a href="<?= Helper_Url::createUrl('authorization/registration'); ?>">Зарегестрироваться</a>
                </li>
                <li><a href="#nav-pwd-dialog" class="popup-text">Забыли пароль?</a>
                </li>
            </ul>
        </form>
    </div>

    <!--        Модальное окно на recall-->
    <div class="mfp-with-anim mfp-hide mfp-dialog clearfix" id="nav-recall-dialog">
        <p class="widget-title none-fw pro-h"><i class="fa fa-sign-in"></i>&nbsp;Перезвоните мне</p>
        <p>Введите номер телефона и наши эксперты свяжуться с Вами в ближайшее время!</p>
        <hr/>
        <form action="<?= Helper_Url::createUrl('authorization/recall') ?>" method="post">
            <div class="form-group">
                <input class="newsletter-input form-control phone" name="phone_number" placeholder="Телефон" type="text" required/>
            </div>
            <input class="btn btn-primary" type="submit" value="Свяжитесь со мной!"/>
        </form>
    </div>
    <!--        Модальное окно на регистрацию-->
    <div class="mfp-with-anim mfp-hide mfp-dialog clearfix" id="nav-account-dialog">
        <p class="widget-title none-fw pro-h">Регистрация</p>
        <p>Зарегестрированым пользователям предоставляется скидка!</p>
        <hr/>
        <form action="<?= Helper_Url::createUrl('authorization/registration'); ?>">
            <div class="form-group">
                <label>Имя</label>
                <input class="form-control" name="name" type="text" required/>
            </div>
            <div class="form-group">
                <label>Фамилия</label>
                <input class="form-control" name="surname" type="text" required/>
            </div>
            <div class="form-group">
                <label>Номер телефона</label>
                <input class="form-control phone" name="phone" type="tel" pattern="2[0-9]{3}-[0-9]{3}">
            </div>
            <div class="form-group">
                <label>Пароль</label>
                <input class="form-control" name="password" type="password" required/>
            </div>
            <div class="form-group">
                <label>Подтверждения пароля</label>
                <input class="form-control" name="password_confirm" type="password" required/>
            </div>
            <div class="form-group">
                <label>Доп. телефон</label>
                <input class="form-control" name="password_confirm" type="text"/>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input class="form-control" name="password_confirm" type="text"/>
            </div>
            <div class="form-group">
                <label>Метод доставки</label>
                <select class="select">
                    <option selected="">Самовывоз</option>
                    <option>Новая почта</option>
                    <option>Интайм</option>
                    <option>Доставка курьером по Киеву</option>
                    <option>Гюнселл</option>
                    <option>Деливери</option>
                    <option>Автолюкс</option>
                </select>
            </div>
            <div class="form-group">
                <label>Адрес доставки</label>
                <textarea class="textarea"></textarea>
            </div>
            <div class="checkbox">
                <label>
                    <input class="i-check" type="checkbox"/>Подписаться на рассылку</label>
            </div>
            <input class="btn btn-primary" type="submit" value="Зарегестрироваться"/>
        </form>
        <div class="gap gap-small"></div>
        <ul class="list-inline">
            <li><a href="#nav-login-dialog" class="popup-text">Вернуться назад</a>
            </li>
        </ul>
    </div>

    <!--        Модальное окно на восстановление пароля-->
    <div class="mfp-with-anim mfp-hide mfp-dialog clearfix" id="nav-pwd-dialog">
        <p class="widget-title none-fw pro-h"><i class="fa fa-key"></i> Восстановление пароля</p>
        <p>Введите Ваш номер телефона, и мы Вам отправим на Ваш e-mail инструкцию по восстановлению пароля</p>
        <hr/>
        <form method="post" action="<?= Helper_Url::createUrl('authorization/password_reset'); ?>">
            <div class="form-group">
                <label>Номер телефона</label>
                <input class="form-control phone" placeholder="+38" type="text" name="phone" required/>
                <!--                    <label>Ваш Email</label>-->
                <!--                    <input name="email" class="form-control" type="text"/>-->
            </div>
            <input class="btn btn-primary" type="submit" value="Восстановить пароль"/>
        </form>
    </div>


    <div class="mfp-with-anim mfp-hide mfp-dialog clearfix" id="social-network-login-dialog">
        <form action="<?= Helper_Url::createUrl('network/registration'); ?>" method="POST">
            <div class="form-group">
                <label>Имя</label>
                <input class="form-control" name="name"  id="social_name" type="text" required/>
            </div>
            <div class="form-group">
                <label>Фамилия</label>
                <input class="form-control" name="surname" id="social_lastname" type="text" required/>
            </div>
            <div class="form-group">
                <label>Номер телефона</label>
                <input class="form-control phone" name="phone" type="tel">
            </div>
            <div class="form-group">
                <label>Пароль</label>
                <input class="form-control" name="password" type="password" required/>
            </div>
            <div class="form-group">
                <label>Подтверждения пароля</label>
                <input class="form-control" name="password_confirm" type="password" required/>
            </div>
            <div class="form-group">
                <label>Доп. телефон</label>
                <input class="form-control" name="additional_phone" type="text"/>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-3">
                        <label>День </label>
                        <select class="form-control" style="width: 60px"
                                name="birth_day">
                            <option label="birth_day" value="18"></option>
                            <?php for($i = 1 ;$i <= 31 ;$i++  ):?>
                                <option value="<?php echo $i ; ?>"><?=$i;?></option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label>Месяц </label>
                        <select class="form-control"  style="width: 60px" name="birth_month">
                            <option label="birth_day" value="">&nbsp;</option>
                            <?php for($i = 1 ;$i <= 12 ;$i++  ):?>
                                <option value="<?php echo $i ; ?>">
                                    <?php echo $i ; ?>
                                </option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label>Год рождения </label>
                        <select class="form-control"  name="birth_year">
                            <option value="">&nbsp;</option>
                            <?php for($i = date("Y")  ;$i >= 1900 ;--$i  ):?>
                                <option label="birth_day" value="<?php echo $i ; ?>"><?php echo $i ; ?></option>
                            <?php endfor;?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input class="form-control" name="email" id="social_email" type="text"/>
                <input type="hidden" id="social_network_id" name="social_network_id">
                <input type="hidden" id="social_network" name="social_network">
            </div>
            <div class="form-group">
                <label>Метод доставки</label>
                <?= Form::select('delivery_method_id', $delivery_methods,null, ['class' => 'form-control']); ?>
            </div>
            <div class="form-group">
                <label>Адрес доставки</label>
                <textarea class="textarea" name="delivery_address"></textarea>
            </div>
            <input class="btn btn-primary" type="submit" value="Зарегестрироваться"/>
        </form>
    </div>

    <div class="container-fluid">
        <div class="row header_index_block">
            <div class="container">
                <div class="row">
                    <div class="first_top_block">
                        <div class="row">
                            <div class="header_logo" >
                                <a href="<?= URL::base(); ?>">
                                    <img style="max-width: 70%; display: block; margin: 0 auto" src="<?= URL::base(); ?>media/img/dist/icons/logo-2.png" alt="Запчасти для иномарок Куряков Eparts" title="Интернет магазин автозапчастей Куряков Eparts"/>
                                </a>
                            </div>
                        </div>
                    </div>


                    <div class="first_second_block">
                        <div class="">
                            <div class="header_phone">
                                <p><br><span class="city_code"><a href="tel:380980928208">(098) 092-82-08</a></span><br></p>
                                <p><span class="city_code"><a href="tel:380951890441">(095) 189-04-41</a></span></p>
<!--                                <p> <span class="city_code"><a href="tel:0800210982">0-800-21-09-82</a></span>-->
<!--                                    <span style="font-size:10px;display: block">Бесплатно по Украине</span> </p>-->
                            </div>
                        </div>
                    </div>
                    <div class=" search_forms">
                        <div class="">
                            <?php echo View::factory('common/search_form')->render(); ?>
                        </div>
                        <div class="">
                            <?php echo View::factory('common/recall_form')->render(); ?>
                        </div>

                    </div>

                    <div class="first_firth_block">


                        <div class="exit col-xs-6">
                            <div class="row">
                                <a href="<?= (ORM::factory('Client')->logged_in()) ? URL::site('orders?archive=all') : '#nav-login-dialog'?>" data-effect="mfp-move-from-top" <?= (ORM::factory('Client')->logged_in()) ? '' : 'class="popup-text"'?>> <img alt="Logo" src="<?= URL::base(); ?>media/img/dist/icons/003-businessman.svg">
                                    <?php if (ORM::factory('Client')->logged_in()) { ?>
                                    <span>Добро пожаловать, <?= ORM::factory('Client')->get_client()->name . " " . ORM::factory('Client')->get_client()->surname ?></span><br><i class="fa fa-sign-in"></i>&nbsp;Личный
                                    кабинет</a>
                                <?php } else { ?>
                                    <span>Добро пожаловать, Гость</span><br><i class="fa fa-sign-in"></i>&nbsp;Войти</a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="cart_home col-xs-6">
                            <div class="row">
                                <a id="show_modal_cart" data-toggle="modal" data-target="#myModal" ><img alt="shopping-basket" src="<?= URL::base(); ?>media/img/dist/icons/001-shopping-basket.svg"><span>Корзина</span><br><span class="cart"><?= Cart::instance()->get_count()['qty']; ?></span> шт.</a>
                                <!-- Button trigger modal -->
                                <button type="button" id="test_modal_cart" style="display: none" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
                                    Launch demo modal
                                </button>
                                <!-- Modal -->
                                <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <span class="block modal-title" id="myModalLabel">Моя корзина</span>
                                            </div>

                                                <div id="bottom"></div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>



    <nav class="navbar navbar-default navbar-main-white navbar-pad-top navbar-first">
        <div class="container">
            <ul class="nav navbar-nav navbar-right navbar-mob-item-left">
                <li>
                    <button class="navbar-toggle collapsed" type="button" data-toggle="collapse"
                            data-target="#main-nav-collapse" data-area_expanded="false"><span
                            class="sr-only">Main Menu</span><span class="icon-bar"></span><span
                            class="icon-bar"></span><span class="icon-bar"></span>
                    </button>
                </li>
            </ul>
        </div>
    </nav>
    <?php echo View::factory('common/top_menu')->render(); ?>
    <div class="container">
        <div class="row">
            <!-- Preloader -->
            <!--            <div id="loading" class="container">-->
            <!--                <div id="loading-center">-->
            <!--                    <div id="loading-center-absolute">-->
            <!--                        <div class="object" id="object_four"></div>-->
            <!--                        <div class="object" id="object_three"></div>-->
            <!--                        <div class="object" id="object_two"></div>-->
            <!--                        <div class="object" id="object_one"></div>-->
            <!--                    </div>-->
            <!--                    <div id="loading-image">-->
            <!--                        <img src="http://ulc.com.ua/media/img/dist/logo-w.png" alt=""/>-->
            <!--                    </div>-->
            <!--                </div>-->
            <!--            </div>-->
            <?= $content; ?>
            <div class="gap"></div>
        </div>
    </div>
</div>
<footer class="main-footer">
    <div class="container">
        <div class="row row-col-gap" data-gutter="60">
            <div class="col-md-3">
                <p class="widget-title-sm none-fw pro-h">Информация</p>
                <ul class="main-footer-list">
                    <li><a href="<?= URL::site("kak-zakazat"); ?>">Как заказать?</a></li>
                    <li><a href="<?= URL::site("sposoby-oplaty-tovara"); ?>">Способы оплаты товара</a></li>
                    <li><a href="<?= URL::site("informaciya-o-dostavke"); ?>">Информация о доставке</a></li>
                    <li><a href="<?= URL::site("politika-vozvrata"); ?>">Политика возврата</a></li>
                    <li><a href="<?= URL::site("optovym-pokupatelyam"); ?>">Оптовым покупателям</a></li>
                    <li><a href="<?= URL::site("postavshchikam"); ?>">Поставщикам</a></li>

                </ul>
            </div>
            <!--noindex-->
                <div class="col-md-3">
                    <span class="widget-title-sm">Обратная связь</span>
                    <form action="<?php echo URL::site('authorization/recall') ?>" method="post">
                        <div class="form-group">
                            <label>Оставьте свой номер телефона, и мы обьязательно с Вами свяжемся</label>
                            <input class="newsletter-input form-control phone" name="phone_number" placeholder="Телефон" type="text" required/>
                        </div>
                        <input class="btn btn-primary" type="submit" value="Свяжитесь со мной!"/>
                    </form>
                </div>
            <!--/noindex-->
            <div class="col-md-3">
                <!--noindex-->
                    <p class="widget-title-sm none-fw pro-h">Социальные сети</p>
                    <p>Интернет-магазин автозапчастей ULC находится во всех популярных социальных сетях.
                        Подписывайтесь!</p>
                <!--/noindex-->
                <ul class="main-footer-social-list">
                    <li>
                        <a class="fa fa-facebook" href="https://www.facebook.com/Магазин-автозапчастей-Eparts-938316732896762/" rel="nofollow"></a>
                    </li>
                    <li>
                        <a class="fa fa-twitter" href="https://twitter.com/EpartsKiev" rel="nofollow"></a>
                    </li>
                    <li>
                        <a class="fa fa-vk" href="https://vk.com/epartskiev" rel="nofollow"></a>
                    </li>
                    <!--                            <li>-->
                    <!--                                <a class="fa fa-pinterest" href="#" rel="nofollow"></a>-->
                    <!--                            </li>-->
                    <!--                            <li>-->
                    <!--                                <a class="fa fa-instagram" href="#" rel="nofollow"></a>-->
                    <!--                            </li>-->
                    <li>
                        <a class="fa fa-google-plus" href="https://plus.google.com/+EpartsKievUaCar" rel="nofollow"></a>
                    </li>
                </ul>
            </div>
            <div class="col-md-3">
                <p class="widget-title-sm none-fw pro-h">Наши телефоны</p>
                <p style="margin-bottom: 10px;">
                    <span class="city_code"></span><a class="tel-footer" href="tel:380980928208">(098) 092-82-08</a><br>
                    <span class="city_code"></span><a class="tel-footer" href="tel:380951890441">(095) 189-04-41</a></p>
<!--                <p><span class="city_code"></span><a href="tel:380950530035">(095) 053-00-35</a><br>-->
<!--                    <span class="city_code"></span><a href="tel:380636318439">(063) 631-84-39</a></p>-->
<!--                <p style="margin-bottom: 10px;"><a class="tel-footer" href="tel:0800210982">0-800-21-09-82</a></p>-->
            </div>
        </div>
        <ul class="main-footer-links-list">
            <li><a href="<?= URL::base(); ?>">Главная</a></li>
            <li><span class="external-reference" data-link="<?= URL::site("katalog"); ?>">Каталог</span></li>
            <li><span class="external-reference" data-link="<?= URL::site("o-nas"); ?>">О нас</span></li>
            <li><span class="external-reference" data-link="<?= URL::site("akcii"); ?>">Акции</span></li>
            <li><span class="external-reference" data-link="<?= URL::site("informaciya-o-dostavke"); ?>">Доставка</span></li>
            <li><span class="external-reference" data-link="<?= URL::site("kontakty"); ?>">Контакты</span></li>
            <li><a href="http://ulc.com.ua/sitemap" class="external-reference" title="Карта сайта">Карта сайта</a></li>

        </ul>
    </div>
</footer>
<div class="copyright-area">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <p class="copyright-text">Магазин автозапчастей &copy; ТОВ ULC 2007-2018.
                    <a href="mailto: Eparts.kiev.ua">ULC.com.ua</a></p>
            </div>
            <div class="col-md-6_1">
                <ul class="payment-icons-list">
                    <li>
                        <img src="<?= URL::base(); ?>media/img/dist/payment/visa-straight-32px.png"
                             alt="Image Alternative text" title="Pay with Visa"/>
                    </li>
                    <li>
                        <img src="<?= URL::base(); ?>media/img/dist/payment/mastercard-straight-32px.png"
                             alt="Image Alternative text" title="Pay with Mastercard"/>
                    </li>
<!--                    <li>-->
<!--                        <img src="--><?//= URL::base(); ?><!--media/img/dist/payment/paypal-straight-32px.png"-->
<!--                             alt="Image Alternative text" title="Pay with Paypal"/>-->
<!--                    </li>-->
<!--                    <li>-->
<!--                        <img src="--><?//= URL::base(); ?><!--media/img/dist/payment/visa-electron-straight-32px.png"-->
<!--                             alt="Image Alternative text" title="Pay with Visa-electron"/>-->
<!--                    </li>-->
<!--                    <li>-->
<!--                        <img src="--><?//= URL::base(); ?><!--media/img/dist/payment/maestro-straight-32px.png"-->
<!--                             alt="Image Alternative text" title="Pay with Maestro"/>-->
<!--                    </li>-->
<!--                    <li>-->
<!--                        <img src="--><?//= URL::base(); ?><!--media/img/dist/payment/discover-straight-32px.png"-->
<!--                             alt="Image Alternative text" title="Pay with Discover"/>-->
<!--                    </li>-->
                </ul>
            </div>
        </div>
    </div>
</div>


<?php
$display_window = false;
if(!Cookie::get('close_modal_info '))
{
    $display_window = true;
}

?>


<?php foreach ($scripts as $script) : ?>
    <script src="<?= URL::base(); ?>media/js/<?= $script; ?>.js"></script>
<?php endforeach; ?>
<script src='https://www.google.com/recaptcha/api.js'></script>

<script>
    $( window ).load(function() {
        $('#loading').fadeOut(1000);
    });
</script>

<script>
    /* <![CDATA[ */
    var google_conversion_id = 941417309;
    var google_custom_params = window.google_tag_params;
    var google_remarketing_only = true;
    /* ]]> */
</script>
<script src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
    <div style="display:inline;">
        <img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/941417309/?guid=ON&amp;script=0"/>
    </div>
</noscript>
<script type="application/ld+json">
		{
		  "@context" : "http://schema.org",
		  "@type" : "Organization",
		  "legalName" : "Интернет-магазин автозапчастей Куряков Eparts",
		  "url" : "http://ulc.com.ua/",
		  "contactPoint" : [{
			"@type" : "ContactPoint",
			"telephone" : "+38(044)361-96-64",
			"contactType" : "customer service"
		  }],
		  "logo" : "http://ulc.com.ua/media/img/dist/logo_w.svg",
		  "sameAs" : [ "https://www.facebook.com/%D0%9C%D0%B0%D0%B3%D0%B0%D0%B7%D0%B8%D0%BD-%D0%B0%D0%B2%D1%82%D0%BE%D0%B7%D0%B0%D0%BF%D1%87%D0%B0%D1%81%D1%82%D0%B5%D0%B9-Eparts-938316732896762/",
			"https://twitter.com/EpartsKiev",
			"https://vk.com/epartskiev",
			"https://plus.google.com/+EpartsKievUaCar"]
		}
	</script>
<script>

    function initMap() {
            if(document.getElementById('map')) {
            var uluru = {lat: 50.43620, lng: 30.409726};
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 15,
                center: uluru
            });
            var marker = new google.maps.Marker({
                position: uluru,
                map: map,
            });
        }
    }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA4YCM8FZ8iiAUdpMVpbEwyS3VFzzQU4Xw&callback=initMap">
</script>
<!--<script>var telerWdWidgetId="87ce6144-ff6e-481c-bed8-6ef78d598da3";var telerWdDomain="eparts-kiev.phonet.com.ua";</script> <script src="//eparts-kiev.phonet.com.ua/public/widget/call-catcher/lib-v3.js"></script>-->

<script>var telerWdWidgetId="87ce6144-ff6e-481c-bed8-6ef78d598da3";var telerWdDomain="eparts-kiev.phonet.com.ua";</script> <script src="//eparts-kiev.phonet.com.ua/public/widget/call-catcher/lib-v3.js"></script>

<noscript><div><img src="https://mc.yandex.ru/watch/11808505" style="position:absolute; left:-9999px;" alt="" /></div></noscript>

</body>


</html>
<?php ob_end_flush(); ?>

