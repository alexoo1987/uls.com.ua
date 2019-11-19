
<?php if ($page->syn == "home") : ?>
    <?php echo View::factory('common/car_select')->render(); ?>
    <?php echo View::factory('common/categories_block')->render(); ?>
    <div class="gap gap-small"></div>
    <div class="row row-sm-gap" data-gutter="10">
        <div class="col-md-2">
            <div class="clearfix">
                <ul class="dropdown-menu dropdown-menu-category dropdown-menu-category-hold dropdown-menu-category-sm">
                    <?php echo View::factory('common/categories_menu')->render(); ?>

                </ul>
            </div>
        </div>
        <div class="col-md-10">
            <div class="owl-carousel owl-loaded owl-nav-dots-inner owl-carousel-curved" data-options='{"items":1,"loop":true}'>
                <div class="owl-item">
                    <div class="slider-item">
                        <div class="slider-item-inner slider-item-inner-container">

                            <p class="text-slider">Высокое качество продукции</p>

                        </div>
                    </div>
                </div>
                <div class="owl-item">
                    <div class="slider-item">
                        <div class="slider-item-inner slider-item-inner-container">

                            <p class="text-slider">Широкий ассортимент товаров</p>

                        </div>
                    </div>
                </div>
                <div class="owl-item">
                    <div class="slider-item">
                        <div class="slider-item-inner slider-item-inner-container">

                            <p class="text-slider">Клиентоориентированный сервис</p>

                        </div>
                    </div>
                </div>
                <div class="owl-item">
                    <div class="slider-item">
                        <div class="slider-item-inner slider-item-inner-container">

                            <p class="text-slider">Разумные цены</p>

                        </div>
                    </div>
                </div>
                <div class="owl-item">
                    <div class="slider-item">
                        <div class="slider-item-inner slider-item-inner-container">

                            <p class="text-slider">Удобный сайт</p>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <h1 class="widget-title-lg">Интернет магазин автозапчастей для иномарок</h1>
    <?php echo View::factory('common/manufacturers')->render(); ?>
    <div style="clear: both"></div>
    <?= $page->content ?>


<?php elseif($page->syn == "zapchasti-na-spectehniku"): ?>
<? if ($message) : ?>
    <h3 class="alert alert-info">
        <?= $message; ?>
    </h3>
<? endif; ?>
    <ol class="breadcrumb page-breadcrumb">
        <li><a href="<?= URL::base()?>">Интернет магазин автозапчастей</a>
        </li>
        <li class="active"><?=$page->h1_title?></li>
    </ol>
    <div class="container spect">

        <h1 class="widget-title-lg">Запчасти на спецтехнику и грузовики</h1>

        <div class="col-md-12">
            <div class="row">
                <?= Form::open('', array('class' => 'form-horizontal', 'id' => 'validate_form')); ?>

                <div class="col-md-6">
                    <div class="form-group">
                        <?= Form::label('phone', 'Телефон*', array('class' => 'control-label ')); ?>
                        <div class="controls">
                            <?= Form::input('phone', '', array('class' => 'form-control phone', 'placeholder' => '+38', 'required' => 'required')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <?= Form::label('name', 'Имя*'); ?>
                        <div class="controls">
                            <input type="text" class="form-control" name="name" id="password" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="control-group">
                        <?= Form::label('comment', 'Комментарий', array('class' => 'control-label')); ?>
                        <div class="controls">
                            <?= Form::textarea('comment', '', array('rows' => '4', 'placeholder' => 'Введите текст')); ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="control-group">
                        <div class="controls">
                            <?= Form::submit('create', 'Отправить', array('class' => 'btn btn-primary')); ?>
                        </div>
                    </div>
                </div>

                <?= Form::close(); ?>
            </div>
        </div>

        <div class="col-md-12" style="margin-top: 5px">
            <?php echo $content; ?>
        </div>

<?php else: ?>
    <ol class="breadcrumb page-breadcrumb">
        <li><a href="<?= URL::base()?>">Интернет магазин автозапчастей</a>
        </li>
        <li class="active"><?=$page->h1_title?></li>
    </ol>
    <div style="clear: both"></div>
    <?= $page->content ?>
<?php endif; ?>


