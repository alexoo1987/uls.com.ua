<div class="container">
	<?php
		if(!$types) { ?>
			<p>Модификации отсутствуют.</p>
		<?php } else { ?>
		<table class="price-table">
		<tr>
			<th style="min-width: 80px;">Название</th>
			<th style="min-width: 80px;">Даты выпуска</th>
			<th style="min-width: 80px;">Объем двигателя</th>
			<th style="min-width: 80px;">Мощность (л.с.)</th>
			<th style="min-width: 80px;">Двигатель</th>
			<th style="min-width: 80px;">Привод</th>
			<th style="min-width: 80px;">Кузов</th>
		</tr>
		<?php
			$manufacturer_slug = $manufacturer['slug'];
			$model_slug = $model['slug'];
		?>
		<?php foreach($types as $type) : ?>
		<tr>			
			<td><a href="<?=URL::site('katalog/'.$manufacturer_slug.'/'.$model_slug.'/'.$type['id']);?>"><?=$type['description']?></a></td>
			<td><a href="<?=URL::site('katalog/'.$manufacturer_slug.'/'.$model_slug.'/'.$type['id']);?>">
			<?php
				$start_year = substr($type['start_date'], 0, 4);
				$start_month = substr($type['start_date'], 4);
				if(!empty($type['end_date'])) {
					$end_year = substr($type['end_date'], 0, 4);
					$end_month = substr($type['end_date'], 4);
				} else {
					$end_year = '.';
					$end_month = '.';
				}
			?>
			<?=$start_month.".".$start_year." - ".$end_month.".".$end_year?>
			</a></td>
			<td><a href="<?=URL::site('katalog/'.$manufacturer_slug.'/'.$model_slug.'/'.$type['id']);?>"><?=$type['capacity']?></a></td>
			<td><a href="<?=URL::site('katalog/'.$manufacturer_slug.'/'.$model_slug.'/'.$type['id']);?>"><?=$type['capacity_hp_from']?></a></td>
			<td><a href="<?=URL::site('katalog/'.$manufacturer_slug.'/'.$model_slug.'/'.$type['id']);?>"><?=$type['engine_type']?></a></td>
			<td><a href="<?=URL::site('katalog/'.$manufacturer_slug.'/'.$model_slug.'/'.$type['id']);?>"><?=$type['drive_type']?></a></td>
			<td><a href="<?=URL::site('katalog/'.$manufacturer_slug.'/'.$model_slug.'/'.$type['id']);?>"><?=$type['body_type']?></a></td>
		</tr>
		<?php endforeach; ?>
	</table>
	<?php } ?>
</div>