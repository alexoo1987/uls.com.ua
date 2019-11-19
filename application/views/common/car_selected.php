<div class="car-select">
	<div class="row" data-gutter="15">
	<div class="col-md-12">
		<br>
		<div class="product">
			<?php
			$car_mod = !empty($car_mod) ? $car_mod : Cookie::get('car_modification', NULL);
			$type = Model::factory('NewTecdoc')->get_info_by_type((integer)$car_mod);
			$type_url = Model::factory('NewTecdoc')->get_url_by_type((integer)$car_mod);
//			var_dump($type_url); exit();
			?>
			<span class="block widget-title-lg" style="margin-bottom: 0px;">Ваш автомобиль:</span>

			<div class="col-md-4" style=" padding-right:0px;">
				<table>
					<tbody><tr>
						<td><p>Производитель:&nbsp;&nbsp;</p></td>
						<td><p><span class="bold"><?=$type['MFA_BRAND']?></span></p></td>
					</tr>
					<tr>
						<td><p>Модель:&nbsp;&nbsp;</p></td>
						<td><p><span class="bold"><?=$type['MOD_CDS_TEXT']?></span></p></td>
					</tr>
					<tr>
						<td><p>Модификация:&nbsp;&nbsp;</p></td>
						<td><span class="bold"><?=$type['TYP_CDS_TEXT']?></span></td>
					</tr>
					<tr>
						<td><p>Дата выпуска:&nbsp;&nbsp;</p></td>
						<td><p><span class="bold"><?php
									$start_year = substr($type['TYP_PCON_START'], 0, 4);
									$start_month = substr($type['TYP_PCON_START'], 4);
									if(!empty($type['TYP_PCON_END'])) {
										$end_year = substr($type['TYP_PCON_END'], 0, 4);
										$end_month = substr($type['TYP_PCON_END'], 4);
									} else {
										$end_year = '.';
										$end_month = '.';
									}
									?>
									<?=$start_month.".".$start_year." - ".$end_month.".".$end_year?></span></p></td>
					</tr>

					</tbody></table>
			</div>
			<div class="col-md-4">
				<table>
					<tbody>
					<?php if(!empty($type['TYP_CYLINDERS'])): ?>
					<tr>
						<td><p>Кол-во цилиндров:&nbsp;&nbsp;</p></td>
						<td><p><span class="bold"><?=$type['TYP_CYLINDERS']?></span></p></td>
					</tr>
					<?php endif;?>
					<?php if(!empty($type['TYP_ENGINE_DES_TEXT'])): ?>
					<tr>
						<td><p>Тип двигателя:&nbsp;&nbsp;</p></td>
						<td><p><span class="bold"><?=$type['TYP_ENGINE_DES_TEXT']?></span></p></td>
					</tr>
					<?php endif;?>
					<?php if(!empty($type['TYP_FUEL_DES_TEXT'])): ?>
					<tr>
						<td><p>Тип топлива:&nbsp;&nbsp;</p></td>
						<td><p><span class="bold"><?=$type['TYP_FUEL_DES_TEXT']?></span></p></td>
					</tr>
					<?php endif;?>
					<?php if(!empty($type['ENG_CODE'])): ?>
						<tr>
							<td><p>Код двигателя:&nbsp;&nbsp;</p></td>
							<td><p><span class="bold"><?=$type['ENG_CODE']?></span></p></td>
						</tr>
					<?php endif;?>

					</tbody></table>
			</div>
			<div class="col-md-4">

				<table>
					<tbody>
				<tr>
					<td><p>Кузов:</p></td>
					<td><span class="bold"><?=$type['TYP_BODY_DES_TEXT']?></span></td>
				</tr>
				<?php if(!empty($type['TYP_HP_FROM']) OR !empty($type['TYP_HP_UPTO'])): ?>
					<tr>
						<td><p>Мощность двигателя (л.с.):</p></td>
						<td><span class="bold"><?=!empty($type['TYP_HP_FROM'])?"от ".$type['TYP_HP_FROM']:""; !empty($type['TYP_HP_UPTO'])?"до ".$type['TYP_HP_UPTO']:"";?></span></td>
					</tr>
				<?php endif;?>
				<?php if(!empty($type['TYP_CCM'])): ?>
					<tr>
						<td><p>Объём двигателя (куб.см):</p></td>
						<td><p><span class="bold"><?=$type['TYP_CCM']?></span></p></td>
					</tr>
				<?php endif;?>


					</tbody></table>


				<p style="text-align: center;">
<!--					<img src="--><?//= URL::base(); ?><!--media/img/car_icon.png">-->
					<a href="#" data-url="<?= URL::site("katalog/select_another"); ?>" class="btn btn-primary select_another" style="margin: 2%;">Изменить выбор</a>
					<?php if(!empty($type_url)): ?>
						<a href="<?=URL::site('katalog/'.$type_url['manuf_url'] . '/'. $type_url['model_url'] . '/'. $type_url['type_url'] );?>" class="btn btn-primary  get_parts">Подобрать запчасти</a>
					<?php endif; ?>
				</p>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
</div>
