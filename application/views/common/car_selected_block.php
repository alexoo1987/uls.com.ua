<!--<div class="car-select">-->
	<div class="col-md-8">
	<div class="product" style="height:491px;  ">
		<?php
		$car_mod = !empty($car_mod) ? $car_mod : Cookie::get('car_modification', NULL);
		$type = Model::factory('NewTecdoc')->get_info_by_type((integer)$car_mod);
		?>
		<div style=" margin: 0 10px;">
			<span class="block" style="text-align: center; font-size: 16px; text-transform: uppercase; font-weight: 600; margin-bottom: 25px;">Ваш автомобиль:</span>
			<div class="col-md-6">
				<p><span>Производитель:</span> <span class="bold"><?=$type['MFA_BRAND']?></span></p>
				<p><span>Модель:</span> <span class="bold"><?=$type['MOD_CDS_TEXT']; //temporarily hide body?></span></p>
				<!--			preg_replace('/\\s*\\([^()]*\\)\\s*/', '', $type['MOD_CDS_TEXT'])-->
				<p><span>Модификация:</span> <span class="bold"><?=$type['TYP_CDS_TEXT']?></span></p>
				<p><span>Даты выпуска:</span> <span class="bold"><?php
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
						<?=$start_month.".".$start_year." - ".$end_month.".".$end_year?></span></p>
				<p><?php if(!empty($type['TYP_CYLINDERS'])): ?><span>Кол-во цилиндров:</span> <span class="bold"><?=$type['TYP_CYLINDERS']?></span><?php endif;?></p>
				<p><?php if(!empty($type['TYP_ENGINE_DES_TEXT'])): ?><span>Тип двигателя:</span> <span class="bold"><?=$type['TYP_ENGINE_DES_TEXT']?></span><?php endif;?></p>
				<p><?php if(!empty($type['TYP_FUEL_DES_TEXT'])): ?><span>Тип топлива:</span> <span class="bold"><?=$type['TYP_FUEL_DES_TEXT']?></span><?php endif;?></p>
			</div>
			<div class="col-md-6">

				<p><?php if(!empty($type['TYP_CCM'])): ?><span>Объём двигателя (куб.см): </span> <span class="bold"><?=$type['TYP_CCM']?></span><?php endif;?></p>
				<p><?php if(!empty($type['ENG_CODE'])): ?><span>Код двигателя: </span> <span class="bold"><?=$type['ENG_CODE']?></span><?php endif;?></p>
				<p><?php if(!empty($type['TYP_HP_FROM']) OR !empty($type['TYP_HP_UPTO'])): ?><span>Мощность двигателя (л.с.): </span> <span class="bold"><?=!empty($type['TYP_HP_FROM'])?"от ".$type['TYP_HP_FROM']:""; !empty($type['TYP_HP_UPTO'])?"до ".$type['TYP_HP_UPTO']:"";?></span><?php endif;?></p>
				<p><span>Кузов:</span> <span class="bold"><?=$type['TYP_BODY_DES_TEXT']?></span></p>
				<img src="<?= URL::base(); ?>media/img/car_icon.png"><br><br>
			</div>
			<div class="col-md-12">
				<p style="text-align: center;">
                    <a href="<?= URL::base(); ?>" data-url="<?= URL::site("katalog/select_another"); ?>" class="btn btn-success btn-lg select_another" style="margin: 2%;background-color: #033788;border-color: #033788;">Изменить выбор</a>
                </p>
			</div>
		</div>
	</div>
</div>
<div class="clearfix"></div>
<!--</div>-->