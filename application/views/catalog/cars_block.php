<div class="clearfix"></div>
<hr>
<?php $manufacturer_brand = strtoupper($manufacturer['brand']); ?>
<p><span class="bold"><?=$category->name?> на другие модели <?=$manufacturer_brand?>:</span></p>
<?php
	$in_column = ceil(count($cars) / 3);
	$count = 0;
	for($column = 0; $column < 3; $column++) {
	?>
		<div class="column3 seo-lists">
			<ul class="no-styled-ul catalog-list">
				<?php
					foreach (array_slice($cars, $column * $in_column, $in_column) as $car) {
				?>
					<li>
						<a href="<?=URL::site('katalog/'.$manufacturer['slug'].'/'.$car['slug'].'/'.$category->slug);?>">
							<?=$category->name?> на <span class="bold"><?=$manufacturer_brand?> <?=$car['short_description']?></span>
						</a>
					</li>
				<?php
					}
				?>
			</ul>
		</div>
	<?php
	}
?>
<div class="clearfix"></div>
<hr>