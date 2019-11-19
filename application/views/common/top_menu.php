<nav class="navbar-default navbar-main-white yamm">
    <div class="container">
        <div class="collapse navbar-collapse navbar-collapse-no-pad" id="main-nav-collapse">
            <ul class="nav navbar-nav">
                <li class="dropdown"><a class="navbar-item-top" href="<?= URL::base(); ?>">Главная</a>
                </li>
                <li class="dropdown top-menu"><span class="navbar-item-top external-reference" data-link="<?= URL::site("katalog"); ?>">Каталог<i
                            class="drop-caret" data-toggle="dropdown"></i></span>
                    <ul class="dropdown-menu dropdown-menu-category">
                    </ul>
                </li>
                <li class="dropdown"><span class="navbar-item-top external-reference" data-link="<?= URL::site("o-nas"); ?>">О нас</span>
                </li>
<!--                <li class="dropdown"><span class="navbar-item-top external-reference" data-link="--><?//= URL::site("promo"); ?><!--">Акции</span>-->
<!--                </li>-->
                <li class="dropdown"><span class="navbar-item-top external-reference" data-link="<?= URL::site("informaciya-o-dostavke"); ?>">Доставка</span>
                </li>
                <li class="dropdown"><span class="navbar-item-top external-reference" data-link="<?= URL::site("kontakty"); ?>">Контакты</span>
                </li>
<!--                <li class="dropdown"><span class="navbar-item-top external-reference new" data-link="--><?//= URL::site("/importing-vehicle"); ?><!--">Пригон авто</span>-->
<!--                </li>-->
<!--                <li class="dropdown"><span class="navbar-item-top external-reference" data-link="--><?//= URL::site("videos/index"); ?><!--">Наши видео</span>-->
<!--                </li>-->
                <li class="dropdown"><span class="navbar-item-top external-reference" data-link="<?= URL::site("vse-otzyvy"); ?>">Все отзывы</span>
                </li>
<!--                <li class="dropdown"><span class="navbar-item-top external-reference" data-link="--><?//= URL::site("vacancies/index"); ?><!--">Горячие вакансии</span>-->
<!--                </li>-->
                <li class="dropdown"><span class="navbar-item-top external-reference" data-link="<?= URL::site("kak-podobrat-zapchasti"); ?>">Как подобрать запчасти?</span>
                </li>
                <li class="dropdown"><span class="navbar-item-top external-reference" data-link="<?= URL::site("zapchasti-na-spectehniku"); ?>">Запчасти СНГ</span>
                </li>
                <li class="dropdown"><a class=" external-reference" title="По бренду авто">По бренду авто</a>
                    <ul class="dropdown-menu manuf_block">
                        <li>
                            <?php echo View::factory('common/manufacturers')->render(); ?>
                        </li>
                    </ul>
                </li>

            </ul>
<!--            <a class="vin_code" href="/">Поиск по VIN коду</a>-->
<!--            <ul class="nav navbar-nav navbar-right">-->
<!---->
<!--                <li class="telephone_header"><span class="telephone_header_index">(044)</span> 361-96-64-->
<!--                </li>-->
<!--                <li class="telephone_header"><span class="telephone_header_index">(067)</span> 291-18-25-->
<!--                </li>-->
<!--                &nbsp-->
<!--                <p>-->
<!--                <li class="telephone_header"><span class="telephone_header_index">(095)</span> 053-00-35-->
<!--                </li>-->
<!--                <li class="telephone_header"><span class="telephone_header_index">(063)</span> 631-84-39-->
<!--                </li>-->
<!--                </p>-->
<!--            </ul class="nav navbar-nav navbar-right">-->
        </div>
    </div>
</nav>