<?php $car_mod =  Cookie::get('car_modification', false)?>
<?php if (!$car_mod) {
	echo View::factory('common/car_select')->render();
}?>
<div class="container categories">
	<?php
		if(!$tree_list) {
	?>
		<p>Древо категорий отсутствует.</p>
	<?php
		} else { ?>
			<div class="gap gap-small"></div>
			<div class="row row-sm-gap" data-gutter="10">
				<h1 class="widget-title-lg"><?=$content_catalog->h1?></h1>
				<ol class="breadcrumb page-breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#">
					<li typeof="v:Breadcrumb"><a href="<?= URL::base()?>" rel="v:url" property="v:title">Интернет магазин автозапчастей</a>
					</li>
					<li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog')?>" rel="v:url" property="v:title">Каталог</a>
					</li>

					<?php if(isset($car['manuf_name']) AND $car['manuf_name']): ?>
						<li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog/'.$car['manuf_url'])?>" rel="v:url" property="v:title"><?=$car['manuf_name'] ?></a>
						</li>
					<?php endif; ?>

					<?php if(isset($car['model_name']) AND $car['model_name']): ?>
						<?php if(isset($car['type_name'])): ?>
							<li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog/'.$car['manuf_url'].'/'.$car['model_url'])?>" rel="v:url" property="v:title"><?=$car['model_name'] ?></a>
							</li>
						<?php else: ?>
							<li class="active" typeof="v:Breadcrumb"><a href="<?= URL::site('katalog').'/'.$car['manuf_url'].'/'.$car['model_url'] ?>" rel="v:url" property="v:title"><?= $car['model_name']?></a></li>
<!--							<li class="active" typeof="v:Breadcrumb"><span rel="v:url" property="v:title">--><?//= $car['model_name']?><!--</span></li>-->
						<?php endif; ?>
					<?php endif; ?>

					<?php if(isset( $car['type_name']) AND $car['type_name']): ?>
<!--						<li class="active" typeof="v:Breadcrumb"><span rel="v:url" property="v:title">--><?//= $car['type_name']?><!--</span></li>-->
						<li class="active" typeof="v:Breadcrumb"><a href="<?= URL::site('katalog').'/'.$car['manuf_url'].'/'.$car['model_url'].'/'.$car['type_url'] ?>" rel="v:url" property="v:title"><?= $car['type_name']?></a></li>
					<?php endif; ?>


				</ol>
				<div class="col-md-2">
					<div class="clearfix">
						<ul class="dropdown-menu dropdown-menu-category dropdown-menu-category-hold dropdown-menu-category-sm">
							<?php echo View::factory('common/categories_menu')->render(); ?>
						</ul>
					</div>
				</div>
				<?php if ($car_mod) {
					echo View::factory('common/car_select')->render();
				}?>
		<?php } ?>
</div>
    <br /><br />

<!--    <div class="clearfix seo-text col-md-12">--><?//= $seo_data->content ?><!--</div>-->
	<div class="clearfix"></div>
	<div class="gap"></div>
	<?php echo View::factory('common/categories_block')->render(); ?>

    <div class="col-md-12" style="margin-top: 5px">
        <?php echo $content_catalog->content; ?>
    </div>
