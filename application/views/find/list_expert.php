<?php $current_url = URL::query(); ?>
<div class="gap"></div>

<a type="button" style="margin: 0px AUTO;margin-bottom: 20px;" href="/find/index<?= $current_url?>" class="btn btn-primary vin_code_long">Стандартный поиск </a><br>
<div class="container">

	<?php $tecdoc = Model::factory('NewTecdoc') ?>
	<?php
	if(count($parts) > 1) { ?>
		<table class="table table-striped table-bordered find-table">
			<tr>
				<th>Производитель</th>
				<th>Артикул</th>
				<th>Наименование</th>
				<th></th>
			</tr>
			<?php foreach($parts as $part) : ?>
				<?php
				//if($part->priceitems->count_all() == 0) continue;
				?>
				<tr>
					<td><?=$part['brand_long']?></td>
					<td><?=$part['article_long']?></td>
					<td><?=Article::shorten_string($part['name'], 3)?></td>
					<td>
						<a class="btn btn-mini" href="<?=URL::site('findexpert/index');?>?art=<?=$part['article']?>&brand=<?=$part['brand']?>"><i class="fa fa-angle-double-right table-shopping-remove" style="font-size: 22px; "></i> </a>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php } elseif(count($parts) == 0) { ?>
		<p>По вашему запросу "<?=$_GET['art']?>" ничего не найдено</p>
	<?php } ?>

	<?php if(count($parts) == 1) : ?>
		<?php foreach($parts as $part): ?>
			<span class='block widget-title-lg'>Искомый артикул</span>
			<table class="table1 expert table-striped table-bordered find-table">
				<thead class="thead1">
					<tr class="tr1">
						<th>Производитель</th>
						<th>Артикул</th>
						<th>Наименование</th>
						<th>Цена</th>
						<th>Доступно на складе</th>
						<th>Срок ожидания (дней)</th>
						<th>Количество</th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<?php $flag = true; ?>
				<?php $count = 1; ?>
				<?php foreach($part['priceitems'] as $price_item) : ?>
					<?php $unique_part = $part['part']['id']; ?>
					<?php if($price_item['amount'] < 0){continue;} ?>
					<?php if($price_item['price_start'] == 0) continue; ?>

					<tr class="test_tag main-row <?=count($part['priceitems']) == $count ? "last" : ""?><?=($price_item['delivery'] == 1 ? " green-row" : "")?><?=(!$flag ? " must-hide" : " non-hide")?>" <?=($flag ? 'data-main="1"' : '')?>>

						<?php if($flag): ?>
							<td class="td4"><b><?=$part['part']['brand_long'] ?></b><br><?=$part['part']['country'] ?></td>
							<td class="td4"><?=$part['part']['article_long'] ?><br><?php if(!empty($part['part']['images'])): ?><img style="width: 50px;" src="<?= URL::base(); ?>image/tecdoc_images<?=$part['part']['images'] ?>" ><?php endif;?></td>
							<td class="td4">
								<?=Article::shorten_string($part['part']['name'], 3)?><br>
							</td>
						<?php else: ?>
							<td class="no-border"></td>
							<td class="no-border"></td>
							<td class="no-border"></td>
						<?php endif; ?>
						<td class="td4">
							<?php if ($guest == true) : ?>
								<?=round(Article::get_price_for_client_by_namber($price_item['price_start']), 0) ;?> грн
							<?php else: ?>
								<?=round(Article::get_price_for_client_by_namber($price_item['price_start']), 0) ;?> грн
							<?php endif ?>
							<?=(isset($price_item['delivery_type']) AND $price_item['delivery_type']) ? "<span title='Возможна дополнительная плата за объем' style='cursor: pointer'><i class=\"icon-plane\"></i></span>": ''?>
						</td>
						<td class="td4">
							<?php $pr_id = $price_item['price_id']; ?>
							<?= (empty($price_item['amount']) ? "<span id ='number_item_$pr_id' class='price_item'>В наличии</span><span class='tooltips' tabindex='0'>&nbsp;~ <span> Неопределенное количество</span></span>" : "Доступно на складе:&nbsp;<span id ='number_item_$pr_id' class='price_item'>". $price_item['amount'] ."</span>&nbsp;шт") ?>
						</td>
						<td class="td4"><?php
							if($price_item['delivery'] >= 5)
							{
								echo '<a role="button" class="popover_link" data-content="Приблизительное время ожидания" data-toggle="modal">~ '.$price_item['delivery'].'</a>';
							}
							else{
								echo $price_item['delivery'];
							}?>
						</td>


						<td class="td4">
							<div class="amount_expert">
								<ul class="product-page-actions-list">
									<li class="product-page-qty-item">
										<button class="product-page-qty product-page-qty-minus">-</button>
										<input id="qty_<?=$price_item['price_id']?>" class="product-page-qty  product-page-qty-input" disabled type="text" value="1" />
										<button class="product-page-qty tooltips  product-page-qty-plus">+<span>Выбрано максимальное количество</span></button>
									</li>
									<li></li>
								</ul>
							</div>
						</td>
						<td class="td4" >
							<?php if(!empty($part['part']['tecdoc_id'])) {?>
								<?php $tecodc_id = $part['part']['tecdoc_id']; ?>
								<a href="#modal_more_<?=$tecodc_id;?>" data-effect="mfp-move-from-top"  class="popup-text" data-content="Информация о запчасти" style="display:inline-block;"><i class="fa fa-info table-shopping-remove" style="font-size: 22px; "></i></a>
								<div class="mfp-with-anim mfp-hide mfp-dialog-long clearfix" id="modal_more_<?=$tecodc_id;?>" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
									<div class="modal-body">
										<ul class="nav nav-tabs">
											<li class="active"><a data-toggle="tab" href="#img_<?=$tecodc_id?>">Изображение</a></li>
											<li><a data-toggle="tab" href="#appl_<?=$tecodc_id?>">Характеристики</a></li>
											<li><a data-toggle="tab" href="#criter_<?=$tecodc_id?>">Применяемость</a></li>
										</ul>
										<div class="tab-content">
											<div id="img_<?=$tecodc_id?>" class="tab-pane fade in active">
												<img src="<?= URL::base(); ?>image/tecdoc_images<?= $part['part']['images'] ?>">
											</div>
											<div id="appl_<?=$tecodc_id?>" class="tab-pane fade">
												<table class="table ">
													<tbody>
													<?php foreach ($tecdoc->get_criterias_by_art_id($tecodc_id) as $criteria): ?>
														<tr>
															<td><?= $criteria['CRITERIA_DES_TEXT'] ?></td>
															<td><?= $criteria['CRITERIA_VALUE_TEXT'] ?></td>
														</tr>
													<?php endforeach; ?>
													</tbody>
												</table>
											</div>
											<div id="criter_<?=$tecodc_id?>" class="tab-pane fade">
												<div style="width: 50%; float: left;">
													<?php $count = 1; $applied_to = $tecdoc->get_cars_by_art_id($tecodc_id); ?>
													<?php foreach ($applied_to as $applied_to_item): ?>
													<?= $applied_to_item['MFA_BRAND'] ?> <?= $applied_to_item['MOD_CDS_TEXT'] ?> <?= $applied_to_item['TYP_CDS_TEXT'] ?>
													<br>
													<?php if ($count > (count($applied_to) / 2)) { ?>
												</div>
												<div style="width: 50%; float: left;">
													<?php } ?>
													<?php $count++; ?>
													<?php endforeach; ?>
												</div>
											</div>
										</div>
									</div>
								</div>


							<?php } ?>
						</td>
						<td class="td4">
							<? if($price_item['price_id']): ?>
								<a class="btn btn-lg price-btn add-to-cart"
								   data-href="<?= URL::site("cart/add"); ?>?price_id=<?= $price_item['price_id'] ?>" data-priceitem="<?= $price_item['price_id'] ?>" onclick="myfunktion()" style="display:inline-block;" ><i class="fa fa-shopping-cart table-shopping-remove"  style="font-size: 22px; "></i></a>
							<? endif; ?>
						</td>

						<td class="td4">
							<?php if ($flag) { ?>
								<button class="btn show_more btn-mini show-more"  style="display:inline-block;"type="button"> <i class="fa fa-angle-double-right table-shopping-remove" style="font-size: 22px; "></i> </button>
							<?php } ?>
						</td>
					</tr>
					<?php $flag = false; ?>
					<?php $count++; ?>
				<?php endforeach; ?>
			</table>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php if (count($final_crosses_original)>1):?>
		<span class='block widget-title-lg'>Оригинальные заменители</span>
		<table class="table1 expert table-striped table-bordered find-table">
			<thead class="thead1">
				<tr class="tr1">
					<th>Производитель</th>
					<th>Артикул</th>
					<th>Наименование</th>
					<th>Цена</th>
					<th>Доступно на складе</th>
					<th>Срок ожидания (дней)</th>
					<th>Количество</th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</thead>
			<?php $flag = true; ?>
			<?php $count = 1; $unique_brand = []; ?>
			<?php foreach ($final_crosses_original as $original_crosses): ?>
				<?php $flag = true; ?>
				<?php $count = 1; ?>
				<?php foreach($original_crosses['priceitems'] as $price_item) : ?>
					<?php if($price_item['price_start'] == 0 OR $price_item['amount'] < 0) continue; ?>
					<tr class="test_tag main-row <?=count($original_crosses['priceitems']) == $count ? "last" : ""?><?=($price_item['delivery'] == 1 ? " green-row" : "")?><?=(!$flag ? " must-hide" : " non-hide")?>" <?=($flag ? 'data-main="1"' : '')?>>
						<?php if($flag): ?>
							<td class="td4" ><b><?=$price_item['brand_long'] ?></b><br><?=$price_item['country'] ?></td>
							<td class="td4" ><?=$price_item['article_long'] ?><br><?php if(!empty($price_item['images'])){ ?><img style="width: 50px;" src="<?= URL::base(); ?>image/tecdoc_images<?=$price_item['images'] ?>" ><? } ?></td>
							<td class="td4" >
								<?=Article::shorten_string($price_item['name'], 3)?><br>
							</td>
						<?php else: ?>
							<td class="no-border"></td>
							<td class="no-border"></td>
							<td class="no-border"></td>
						<?php endif; ?>

						<td class="td4" >
							<?=round(Article::get_price_for_client_by_namber($price_item['price_start'], $discount_id), 2) ;?> грн.
						</td>
						<td class="td4">
							<?php $pr_id = $price_item['price_id']; ?>
							<?= (empty($price_item['amount']) ? "<span id ='number_item_$pr_id' class='price_item'>В наличии</span><span class='tooltips' tabindex='0'>&nbsp;~ <span> Неопределенное количество</span></span>" : "Доступно на складе:&nbsp;<span id ='number_item_$pr_id' class='price_item'>". $price_item['amount'] ."</span>&nbsp;шт") ?>
						</td>
						<td class="td4"><?php
							if($price_item['delivery'] >= 5)
							{
								echo '<a role="button" class="popover_link" data-content="Приблизительное время ожидания" data-toggle="modal">~ '.$price_item['delivery'].'</a>';
							}
							else{
								echo $price_item['delivery'];
							}?>
						</td>

						<td class="td4">
							<div class="amount_expert">
								<ul class="product-page-actions-list">
									<li class="product-page-qty-item">
										<button class="product-page-qty product-page-qty-minus">-</button>
										<input id="qty_<?=$price_item['price_id']?>" class="product-page-qty  product-page-qty-input" disabled type="text" value="1" />
										<button class="product-page-qty tooltips  product-page-qty-plus">+<span>Выбрано максимальное количество</span></button>
									</li>
									<li></li>
								</ul>
							</div>
						</td>
						<td class="td4" >
							<?php if(!empty($price_item['tecdoc_id'])) {?>
								<?php $tecodc_id = $price_item['tecdoc_id']; ?>
								<a href="#modal_more_<?=$tecodc_id;?>" data-effect="mfp-move-from-top"  class="popup-text" data-content="Информация о запчасти" style="display:inline-block;"><i class="fa fa-info table-shopping-remove" style="font-size: 22px; "></i></a>
								<div class="mfp-with-anim mfp-hide mfp-dialog-long clearfix" id="modal_more_<?=$tecodc_id;?>" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
									<div class="modal-body">
										<ul class="nav nav-tabs">
											<li class="active"><a data-toggle="tab" href="#img_<?=$tecodc_id?>">Изображение</a></li>
											<li><a data-toggle="tab" href="#appl_<?=$tecodc_id?>">Характеристики</a></li>
											<li><a data-toggle="tab" href="#criter_<?=$tecodc_id?>">Применяемость</a></li>
										</ul>
										<div class="tab-content">
											<div id="img_<?=$tecodc_id?>" class="tab-pane fade in active">
												<img src="<?= URL::base(); ?>image/tecdoc_images<?= $part['part']['images'] ?>">
											</div>
											<div id="appl_<?=$tecodc_id?>" class="tab-pane fade">
												<table class="table ">
													<tbody>
													<?php foreach ($tecdoc->get_criterias_by_art_id($tecodc_id) as $criteria): ?>
														<tr>
															<td><?= $criteria['CRITERIA_DES_TEXT'] ?></td>
															<td><?= $criteria['CRITERIA_VALUE_TEXT'] ?></td>
														</tr>
													<?php endforeach; ?>
													</tbody>
												</table>
											</div>
											<div id="criter_<?=$tecodc_id?>" class="tab-pane fade">
												<div style="width: 50%; float: left;">
													<?php $count = 1; $applied_to = $tecdoc->get_cars_by_art_id($tecodc_id); ?>
													<?php foreach ($applied_to as $applied_to_item): ?>
													<?= $applied_to_item['MFA_BRAND'] ?> <?= $applied_to_item['MOD_CDS_TEXT'] ?> <?= $applied_to_item['TYP_CDS_TEXT'] ?>
													<br>
													<?php if ($count > (count($applied_to) / 2)) { ?>
												</div>
												<div style="width: 50%; float: left;">
													<?php } ?>
													<?php $count++; ?>
													<?php endforeach; ?>
												</div>
											</div>
										</div>
									</div>
								</div>


							<?php } ?>
						</td>
						<td class="td4">
							<? if($price_item['price_id']): ?>
								<a class="btn btn-lg price-btn add-to-cart"
								   data-href="<?= URL::site("cart/add"); ?>?price_id=<?= $price_item['price_id'] ?>" data-priceitem="<?= $price_item['price_id'] ?>" onclick="myfunktion()" style="display:inline-block;" ><i class="fa fa-shopping-cart table-shopping-remove"  style="font-size: 22px; "></i></a>
							<? endif; ?>
						</td>

						<td class="td4">
							<?php if ($flag  AND count($original_crosses['priceitems'])>1) { ?>
								<button class="btn show_more btn-mini show-more"  style="display:inline-block;"type="button"> <i class="fa fa-angle-double-right table-shopping-remove" style="font-size: 22px; "></i> </button>
							<?php } ?>
						</td>
					</tr>
					<?php $flag = false; ?>
					<?php $count++; ?>
				<?php endforeach; ?>
			<?php endforeach; ?>
		</table>
	<?php endif; ?>

	<?php if (count($final_crosses_analog)>1):?>
		<span class='block widget-title-lg'>Аналоги заменители</span>
		<table class="table1 expert table-striped table-bordered find-table">
			<thead class="thead1">
			<tr class="tr1">
				<th>Производитель</th>
				<th>Артикул</th>
				<th>Наименование</th>
				<th>Цена</th>
				<th>Доступно на складе</th>
				<th>Срок ожидания (дней)</th>
				<th>Количество</th>
				<th></th>
				<th></th>
				<th></th>
			</tr>
			</thead>
			<?php $flag = true; ?>
			<?php $count = 1; $unique_brand = []; ?>
			<?php foreach ($final_crosses_analog as $original_crosses): ?>
				<?php $flag = true; ?>
				<?php $count = 1; ?>
				<?php foreach($original_crosses['priceitems'] as $price_item) : ?>
					<?php if($price_item['price_start'] == 0 OR $price_item['amount'] < 0) continue; ?>
					<tr class="test_tag main-row <?=count($original_crosses['priceitems']) == $count ? "last" : ""?><?=($price_item['delivery'] == 1 ? " green-row" : "")?><?=(!$flag ? " must-hide" : " non-hide")?>" <?=($flag ? 'data-main="1"' : '')?>>
						<?php if($flag): ?>
							<td class="td4" ><b><?=$price_item['brand_long'] ?></b><br><?=$price_item['country'] ?></td>
							<td class="td4" ><?=$price_item['article_long'] ?><br><?php if(!empty($price_item['images'])){ ?><img style="width: 50px;" src="<?= URL::base(); ?>image/tecdoc_images<?=$price_item['images'] ?>" ><? } ?></td>
							<td class="td4" >
								<?=Article::shorten_string($price_item['name'], 3)?><br>
							</td>
						<?php else: ?>
							<td class="no-border"></td>
							<td class="no-border"></td>
							<td class="no-border"></td>
						<?php endif; ?>

						<td class="td4" >
							<?=round(Article::get_price_for_client_by_namber($price_item['price_start'], $discount_id), 2) ;?> грн.
						</td>
						<td class="td4">
							<?php $pr_id = $price_item['price_id']; ?>
							<?= (empty($price_item['amount']) ? "<span id ='number_item_$pr_id' class='price_item'>В наличии</span><span class='tooltips' tabindex='0'>&nbsp;~ <span> Неопределенное количество</span></span>" : "Доступно на складе:&nbsp;<span id ='number_item_$pr_id' class='price_item'>". $price_item['amount'] ."</span>&nbsp;шт") ?>
						</td>
						<td class="td4"><?php
							if($price_item['delivery'] >= 5)
							{
								echo '<a role="button" class="popover_link" data-content="Приблизительное время ожидания" data-toggle="modal">~ '.$price_item['delivery'].'</a>';
							}
							else{
								echo $price_item['delivery'];
							}?>
						</td>

						<td class="td4">
							<div class="amount_expert">
								<ul class="product-page-actions-list">
									<li class="product-page-qty-item">
										<button class="product-page-qty product-page-qty-minus">-</button>
										<input id="qty_<?=$price_item['price_id']?>" class="product-page-qty  product-page-qty-input" disabled type="text" value="1" />
										<button class="product-page-qty tooltips  product-page-qty-plus">+<span>Выбрано максимальное количество</span></button>
									</li>
									<li></li>
								</ul>
							</div>
						</td>
						<td class="td4" >
							<?php if(!empty($price_item['tecdoc_id'])) {?>
								<?php $tecodc_id = $price_item['tecdoc_id']; ?>
								<a href="#modal_more_<?=$tecodc_id;?>" data-effect="mfp-move-from-top"  class="popup-text" data-content="Информация о запчасти" style="display:inline-block;"><i class="fa fa-info table-shopping-remove" style="font-size: 22px; "></i></a>
								<div class="mfp-with-anim mfp-hide mfp-dialog-long clearfix" id="modal_more_<?=$tecodc_id;?>" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
									<div class="modal-body">
										<ul class="nav nav-tabs">
											<li class="active"><a data-toggle="tab" href="#img_<?=$tecodc_id?>">Изображение</a></li>
											<li><a data-toggle="tab" href="#appl_<?=$tecodc_id?>">Характеристики</a></li>
											<li><a data-toggle="tab" href="#criter_<?=$tecodc_id?>">Применяемость</a></li>
										</ul>
										<div class="tab-content">
											<div id="img_<?=$tecodc_id?>" class="tab-pane fade in active">
												<img src="<?= URL::base(); ?>image/tecdoc_images<?= $part['part']['images'] ?>">
											</div>
											<div id="appl_<?=$tecodc_id?>" class="tab-pane fade">
												<table class="table ">
													<tbody>
													<?php foreach ($tecdoc->get_criterias_by_art_id($tecodc_id) as $criteria): ?>
														<tr>
															<td><?= $criteria['CRITERIA_DES_TEXT'] ?></td>
															<td><?= $criteria['CRITERIA_VALUE_TEXT'] ?></td>
														</tr>
													<?php endforeach; ?>
													</tbody>
												</table>
											</div>
											<div id="criter_<?=$tecodc_id?>" class="tab-pane fade">
												<div style="width: 50%; float: left;">
													<?php $count = 1; $applied_to = $tecdoc->get_cars_by_art_id($tecodc_id); ?>
													<?php foreach ($applied_to as $applied_to_item): ?>
													<?= $applied_to_item['MFA_BRAND'] ?> <?= $applied_to_item['MOD_CDS_TEXT'] ?> <?= $applied_to_item['TYP_CDS_TEXT'] ?>
													<br>
													<?php if ($count > (count($applied_to) / 2)) { ?>
												</div>
												<div style="width: 50%; float: left;">
													<?php } ?>
													<?php $count++; ?>
													<?php endforeach; ?>
												</div>
											</div>
										</div>
									</div>
								</div>


							<?php } ?>
						</td>
						<td class="td4">
							<? if($price_item['price_id']): ?>
								<a class="btn btn-lg price-btn add-to-cart"
								   data-href="<?= URL::site("cart/add"); ?>?price_id=<?= $price_item['price_id'] ?>" data-priceitem="<?= $price_item['price_id'] ?>" onclick="myfunktion()" style="display:inline-block;" ><i class="fa fa-shopping-cart table-shopping-remove"  style="font-size: 22px; "></i></a>
							<? endif; ?>
						</td>

						<td class="td4">
							<?php if ($flag  AND count($original_crosses['priceitems'])>1) { ?>
								<button class="btn show_more btn-mini show-more"  style="display:inline-block;"type="button"> <i class="fa fa-angle-double-right table-shopping-remove" style="font-size: 22px; "></i> </button>
							<?php } ?>
						</td>
					</tr>
					<?php $flag = false; ?>
					<?php $count++; ?>
				<?php endforeach; ?>
			<?php endforeach; ?>
		</table>
	<?php endif; ?>
</div>



<script type="text/javascript">
	function myfunktion(){
		$.notify({
				// options
				icon: 'fa fa-shopping-cart',
				message: 'Товар добавлен в корзину. <br>Нажмите сюда, что бы перейти в корзину.',
				url: '/cart/show',
				target: '_blank'
			},{
				// settings
				element: 'body',
				position: null,
				type: "success",
				allow_dismiss: false,
				newest_on_top: false,
				showProgressbar: false,
				placement: {
					from: "bottom",
					align: "right"
				},
				offset: 20,
				spacing: 10,
				z_index: 1031,
				delay: 5000,
				timer: 1000,
				url_target: '_blank',
				mouse_over: null,
				animate: {
					enter: 'animated fadeInDown',
					exit: 'animated fadeOutUp'
				},
				onShow: null,
				onShown: null,
				onClose: null,
				onClosed: null,
				icon_type: 'class',
				template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0}" role="alert">' +
				'<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
				'<span data-notify="icon"></span> ' +
				'<span data-notify="title">{1}</span> ' +
				'<span data-notify="message">{2}</span>' +
				'<div class="progress" data-notify="progressbar">' +
				'<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
				'</div>' +
				'<a href="{3}" data-notify="url"></a>' +
				'</div>'
			}
		);}
</script>
