<div class="container">
	<?php echo View::factory('common/car_select')->render(); ?>
        <ol class="breadcrumb page-breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#">
            <li typeof="v:Breadcrumb"><a href="<?= URL::base()?>" rel="v:url" property="v:title">Интернет магазин автозапчастей</a>
            </li>
            <li class="active" typeof="v:Breadcrumb"><span rel="v:url" property="v:title">Каталог</span></li>
        </ol>

	<?php echo View::factory('common/categories_block')->set('content_catalog', $content_catalog)->render(); ?>
	        <span class="block widget-title-lg">Запчасти для автомобилей</span>
	<?php echo View::factory('common/manufacturers')->render(); ?>

        <div class="col-md-12" style="margin-top: 5px">
            <?php echo $content_catalog->content; ?>
        </div>

</div>