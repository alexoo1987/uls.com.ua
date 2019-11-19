<?php $tecdoc = Model::factory('Tecdoc'); ?>
<p><span class="bold">Мы продаем <?=$category->name?> на следующие автомобили:</span></p>
<?php
	$in_column = ceil(count($manufacturers) / 3);
	$count = 0;
	for($column = 0; $column < 3; $column++) {
	?>
		<div class="column3 seo-lists">
			<ul class="no-styled-ul catalog-list">
				<?php
					foreach (array_slice($manufacturers, $column * $in_column, $in_column) as $manufacturer) {
				?>
					<li>
						<?php $manufacturer_brand = strtoupper($manufacturer['brand']); ?>
						<p><span class="bold"><?=$manufacturer_brand?></span></p>
						<?php $models = $tecdoc->get_cars(false, $manufacturer['id']); ?>
						<?php if($models): ?>
						<ul class="model_list_under_manufacturer">
							<?php $showed = 0; ?>
							<?php foreach($models as $model): ?>
								<li class="<?php if($showed >= 5): ?>model_not_displayed<?php endif; ?>">
									<a href="<?=URL::site('katalog/'.$manufacturer['slug'].'/'.$model['slug'].'/'.$category->slug);?>">
										<?=$manufacturer_brand?> <?=$model['short_description']?>
									</a>
								</li>
								<?php $showed++; ?>
							<?php endforeach; ?>
							<?php if($showed > 5): ?><li class="model_show_more"><a href="#">Показать еще...</a></li><?php endif; ?>
						</ul>
						<?php endif; ?>
					</li>
				<?php
					}
				?>
			</ul>
		</div>
	<?php
	}
?>