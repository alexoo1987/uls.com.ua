<?php echo View::factory('common/car_select')->render(); ?>
<div class="container">
	<?php
		if(!$manufacturer): ?>
			<p>Модели отсутствуют.</p>
		<?php else: ?>
			<h1 class="widget-title-lg"><?=$content_catalog->h1?></h1>
			<ol class="breadcrumb page-breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#">
				<li typeof="v:Breadcrumb"><a href="<?= URL::base()?>" rel="v:url" property="v:title">Интернет магазин автозапчастей</a>
				</li>
				<li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog')?>" rel="v:url" property="v:title">Каталог</a>
				</li>
				<li class="active" typeof="v:Breadcrumb"><span rel="v:url" property="v:title"><?= $manufacturer[0]['brand'] ?></span></li>
			</ol>
            <?php
            $in_column = ceil(count($manufacturer) / 3);
            $count = 0;
            for ($column = 0; $column < 3; $column++) : ?>
			<div class="col-md-4">
				<ul class="no-styled-ul new_manuf_list catalog-list">
					<?php foreach (array_slice($manufacturer, $column * $in_column, $in_column) as $value):?>
						<li>
							<a href="<?= Helper_Url::createUrl('katalog/'.$value['url_manufact'].'/'.$value['url_model']);?>">
								<span class="dist"><?=$value['brand']?> <?=$value['model']?></span><?php /*<br>
								(<?=$start_month.".".$start_year." - ".$end_month.".".$end_year?>) */?>
							</a>
						</li>
					<?php endforeach;?>
                </ul>
            </div>
        <?php endfor; ?>
    <?php endif;?>
<!--	<div class="clearfix seo-text col-md-12">--><?//= $seo_data->content ?><!--</div>-->
</div>

<div class="col-md-12" style="margin-top: 5px">
    <?php echo $content_catalog->content; ?>
</div>

<?php //echo View::factory('common/categories_block')->render(); ?>
