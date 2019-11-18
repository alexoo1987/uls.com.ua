<div class="container">
	<?= Form::open('', array('class' => 'form-horizontal price-level', 'method' => 'get')); ?>
	<? if(ORM::factory('Permission')->checkPermission('find_show_sale')) { ?>
		<?= Form::hidden('art', Arr::get($_GET, 'art')); ?>
		<?= Form::hidden('brand', Arr::get($_GET, 'brand')); ?>
		Уровень цен <?= Form::select('discount_id', $discounts, $discount_id); ?>&nbsp; &nbsp; &nbsp;Показать только <?= Form::select('delivery_type', $deliverys, $delivery_type); ?><br>
		<!--			Сортировать по --><?//= Form::select('order_by', $order_by, $order_by_str); ?><!--<br />-->
		<?php if($discount_id) { ?>
			Показан уровень цен "<?=$discounts[$discount_id]?>" <!--a href="<?=URL::site('admin/find/index');?>?art=<?=Arr::get($_GET, 'art')?>&brand=<?=Arr::get($_GET, 'brand')?>">отмена</a-->
		<?php } ?>
	<?php } ?>
	<?= Form::close(); ?>


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
				<tr>
					<td><?=$part['brand_long']?></td>
					<td><?=$part['article_long']?></td>
					<td><?=Article::shorten_string($part['name'], 3)?></td>
					<td>
						<a class="btn btn-mini" href="<?=URL::site('admin/find/index');?>?art=<?=$part['article']?>&brand=<?=urlencode($part['brand'])?>&discount_id=<?=($_GET['discount_id'] ? $_GET['discount_id'] : $discount_id)?>">>>></a>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php } elseif(count($parts) == 0) { ?>
		<p>По вашему запросу "<?=$_GET['art']?>" ничего не найдено</p>
	<?php } ?>

	<?php if(count($parts) == 1): ?>
		<?php $hide_purchase = Cookie::get('hide_purchase', '0') == '1'; ?>
		<? if(ORM::factory('Permission')->checkPermission('find_show_purchase')) { ?>
			<p><input name="hide_purchase" id="hide_purchase" type="checkbox" data-url="<?=URL::site('admin/find/hide_purchase');?>" <?=$hide_purchase ? ' checked="checked"' : "" ?> />Скрыть входящую цену</p>
		<?php } ?>
		<?php foreach($parts as $part): ?>

			<b>Искомый артикул</b>
			<table class="table table-striped table-bordered find-table">
				<tr>
					<th>Производитель</th>
					<th>Артикул</th>
					<th>Наименование</th>
					<? if(ORM::factory('Permission')->checkPermission('find_show_purchase')) { ?>
						<th class="purchase-column" style="<?=$hide_purchase ? "display: none;" : "" ?>">Цена закупки</th>
					<?php } ?>
					<? if(ORM::factory('Permission')->checkPermission('find_show_sale')) { ?>
						<th>Цена продажи</th>
					<?php } ?>
					<th>Кол-во</th>
					<th>Ожидание (дней)</th>
					<th>Примечания</th>
					<? if(ORM::factory('Permission')->checkPermission('find_show_supplier') && Auth::instance()->get_user()->id != 171) { ?>
						<th>Поставщик</th>
					<?php } ?>
					<th></th>
					<th></th>
				</tr>
				<?php $flag = true; ?>
				<?php $count = 1; ?>
				<?php foreach($part['priceitems'] as $price_item) : ?>
					<?php $unique_part = $part['part']['id']; ?>
					<?php if($price_item['amount'] < 0){continue;} ?>
					<?php if($price_item['price_start'] == 0) continue; ?>
					<tr class="main-row <?=count($part['priceitems']) == $count ? "last" : ""?><?=($price_item['delivery'] == 1 ? " green-row" : "")?><?=(!$flag ? " must-hide" : " non-hide")?>" <?=($flag ? 'data-main="1"' : '')?>>

						<?php if($flag): ?>
							<td><b><?=$part['part']['brand_long'] ?></b><br><?=$part['part']['country'] ?></td>
							<td><?=$part['part']['article_long'] ?><br><?php if(!empty($part['part']['images'])): ?><img style="width: 50px;" src="<?= URL::base(); ?>image/tecdoc_images<?=$part['part']['images'] ?>" ><?php endif;?></td>
							<td>
								<?=Article::shorten_string($part['part']['name'], 3)?><br>
								<?= $part['part']['tecdoc_id'];  ?>
								<?php if (!empty($part['part']['tecdoc_id'])) : ?>
									<?php $tecodc_id = $part['part']['tecdoc_id']; ?>
									<a href="#position_log_<?=$tecodc_id;?>" role="button" class="btn btn-mini popover_link" data-content="Информация о запчасти" data-toggle="modal"><i class="icon-info-sign"></i> Инфо</a>
									<div id="position_log_<?=$tecodc_id;?>" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
											<h3>Info</h3>
										</div>
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
								<?php endif?>

							</td>
						<?php else: ?>
							<td class="no-border"></td>
							<td class="no-border"></td>
							<td class="no-border"></td>
						<?php endif; ?>
						<? if(ORM::factory('Permission')->checkPermission('find_show_purchase')) { ?>
							<td class="purchase-column" style="<?=$hide_purchase ? "display: none;" : "" ?>">
								<?=$price_item['price_start']?> грн.
								<?=(isset($price_item['weight']) ? "<br/> Вес: ".$price_item['weight']." кг": '')?>
							</td>
						<?php } ?>
						<? if(ORM::factory('Permission')->checkPermission('find_show_sale')) { ?>
							<td>
								<?=round(Article::get_price_for_client_by_namber($price_item['price_start'], $discount_id), 2) ;?> грн.
								<?=(isset($price_item['delivery_type']) AND $price_item['delivery_type']) ? "<span title='Возможна дополнительная плата за объем' style='cursor: pointer'><i class=\"icon-plane\"></i></span>": ''?>
							</td>
						<?php } ?>
						<td><?=$price_item['amount']?> </td>
						<td><?php
							if($price_item['delivery'] >= 5)
							{
								echo '<a href="#orderitem_log" role="button" class="popover_link" data-content="Приблизительное время ожидания" data-toggle="modal">~ '.$price_item['delivery'].'</a>';
							}
							else{
								echo $price_item['delivery'];
							}?>

							<?php
							if($price_item['delivery'] >= 3)
							{
								echo '<p style="color: red; font-size: 10px; line-height: 10px">заказ товара при полной оплате</p>';
							}?>
						</td>
						<td>
							<?php if($price_item['supplier_id']==38)
							{
								if($price_item['return_flag'] == 'Y')
								{
									echo "Существует возможность возврата";
								}
								else
								{
									echo "Нет возвратов";
								}
							}
							else
							{
								echo $price_item['notice'];
							}?>
						</td>
						<? if(ORM::factory('Permission')->checkPermission('find_show_supplier') && Auth::instance()->get_user()->id != 171) : ?>
							<td>
                                <?php if(ORM::factory('Permission')->checkRole('manager') || ORM::factory('Permission')->checkRole('Руководитель отделения продаж')): ?>
                                    <a href="#"><?=$price_item['supplier_id']?></a>
                                <?php else:?>
                                    <? if(ORM::factory('Permission')->checkPermission('supplier_information')) : ?>
                                        <a href="#" class="popover_link" rel="popover" data-placement="bottom" data-content="<?=addcslashes($price_item['supplier_name']."<br>".$price_item['supplier_name'], '"')?>"><?=$price_item['supplier_name']?></a>
                                    <?php else:?>
                                        <a href="#" class="popover_link" rel="popover" data-placement="bottom" data-content=""><?=$price_item['supplier_name']?></a>
                                    <?php endif;?>
                                <?php endif;?>
                            </td>
                        <?php endif;?>
						<td>
							<? if($price_item['price_id']): ?>
								<a class="btn btn-mini" href="<?=URL::site('admin/orders/add_by_price_id?priceitem_id='.$price_item['price_id'].$discount_str);?>"><i class="icon-shopping-cart"></i> Добавить в заказ</a>
							<? else: ?>
								---
							<? endif; ?>
						</td>
						<td>
							<?php if ($flag) { ?>
								<button class="btn btn-mini btn-primary show-more" type="button">Еще >>></button>
							<?php } ?>
						</td>
					</tr>
					<?php $flag = false; ?>
					<?php $count++; ?>
				<?php endforeach; ?>
			</table>


			<!-- Modal -->
			<div id="orderitem_log" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3>Приблизительное время ожидания</h3>
				</div>
				<div class="modal-body">
				</div>
			</div>
		<?php endforeach; ?>


		<?php if (count($final_crosses_original)>0):?>
			<b>Оригинальные заменители</b>
			<table class="table table-striped table-bordered find-table">
				<tr>
					<th>Производитель</th>
					<th>Артикул</th>
					<th>Наименование</th>
					<? if(ORM::factory('Permission')->checkPermission('find_show_purchase')) { ?>
						<th class="purchase-column" style="<?=$hide_purchase ? "display: none;" : "" ?>">Цена закупки</th>
					<?php } ?>
					<? if(ORM::factory('Permission')->checkPermission('find_show_sale')) { ?>
						<th>Цена продажи</th>
					<?php } ?>
					<th>Кол-во</th>
					<th>Ожидание (дней)</th>
					<th>Примечания</th>
					<? if(ORM::factory('Permission')->checkPermission('find_show_supplier') && Auth::instance()->get_user()->id != 171) { ?>
						<th>Поставщик</th>
					<?php } ?>
					<th></th>
					<th></th>
					<th></th>
				</tr>
				<?php $flag = true; ?>
				<?php $count = 1; $unique_brand = []; ?>
				<?php foreach ($final_crosses_original as $original_crosses): ?>
					<?php $flag = true; ?>
					<?php $count = 1; ?>
					<?php foreach($original_crosses['priceitems'] as $price_item) : ?>
						<?php if($price_item['price_start'] == 0 OR $price_item['amount'] < 0) continue; ?>
						<tr class="main-row <?=count($original_crosses['priceitems']) == $count ? "last" : ""?><?=($price_item['delivery'] == 1 ? " green-row" : "")?><?=(!$flag ? " must-hide" : " non-hide")?>" <?=($flag ? 'data-main="1"' : '')?>>
							<?php if($flag): ?>
								<td><b><?=$price_item['brand_long'] ?></b><br><?=$price_item['country'] ?></td>
								<td><?=$price_item['article_long'] ?><br><?php if(!empty($price_item['images'])){ ?><img style="width: 50px;" src="<?= URL::base(); ?>image/tecdoc_images<?=$price_item['images'] ?>" ><? } ?></td>
								<td>
									<?=Article::shorten_string($price_item['name'], 3)?><br>
									<?= $price_item['tecdoc_id'];  ?>
									<?php if (!empty($price_item['tecdoc_id'])) : ?>
										<?php $tecodc_id = $price_item['tecdoc_id']; ?>
										<a href="#position_log_<?=$tecodc_id;?>" role="button" class="btn btn-mini popover_link" data-content="Информация о запчасти" data-toggle="modal"><i class="icon-info-sign"></i> Инфо</a>
										<div id="position_log_<?=$tecodc_id;?>" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
												<h3>Info</h3>
											</div>
											<div class="modal-body">
												<ul class="nav nav-tabs">
													<li class="active"><a data-toggle="tab" href="#img_<?=$tecodc_id?>">Изображение</a></li>
													<li><a data-toggle="tab" href="#appl_<?=$tecodc_id?>">Характеристики</a></li>
													<li><a data-toggle="tab" href="#criter_<?=$tecodc_id?>">Применяемость</a></li>
												</ul>
												<div class="tab-content">
													<div id="img_<?=$tecodc_id?>" class="tab-pane fade in active">
														<img src="<?= URL::base(); ?>image/tecdoc_images<?= $price_item['images'] ?>">
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
									<?php endif?>

								</td>
							<?php else: ?>
								<td class="no-border"></td>
								<td class="no-border"></td>
								<td class="no-border"></td>
							<?php endif; ?>
							<? if(ORM::factory('Permission')->checkPermission('find_show_purchase')) { ?>
								<td class="purchase-column" style="<?=$hide_purchase ? "display: none;" : "" ?>">
									<?=round($price_item['price_start'],2)?> грн.
									<?=(isset($price_item['weight']) ? "<br/> Вес: ".$price_item['weight']." кг": '')?>
								</td>
							<?php } ?>
							<? if(ORM::factory('Permission')->checkPermission('find_show_sale')) { ?>
								<td>
									<?=round(Article::get_price_for_client_by_namber($price_item['price_start'], $discount_id), 2) ;?> грн.
									<?=(isset($price_item['delivery_type']) AND $price_item['delivery_type']) ? "<span title='Возможна дополнительная плата за объем' style='cursor: pointer'><i class=\"icon-plane\"></i></span>": ''?>
								</td>
							<?php } ?>
							<td><?=$price_item['amount']?> </td>
							<td><?php
								if($price_item['delivery'] >= 5)
								{
									echo '<a href="#orderitem_log" role="button" class="popover_link" data-content="Приблизительное время ожидания" data-toggle="modal">~ '.$price_item['delivery'].'</a>';
								}
								else{
									echo $price_item['delivery'];
								}?>

								<?php
								if($price_item['delivery'] >= 3)
								{
									echo '<p style="color: red; font-size: 10px; line-height: 10px">заказ товара при полной оплате</p>';
								}?>
							</td>
							<td>
								<?php if($price_item['supplier_id']==38)
								{
									if($price_item['return_flag'] == 'Y')
									{
										echo "Существует возможность возврата";
									}
									else
									{
										echo "Нет возвратов";
									}
								}
								else
								{
									echo $price_item['notice'];
								}?>
							</td>
                            <? if(ORM::factory('Permission')->checkPermission('find_show_supplier') && Auth::instance()->get_user()->id != 171) : ?>
                                <td>
                                    <?php if(ORM::factory('Permission')->checkRole('manager') || ORM::factory('Permission')->checkRole('Руководитель отделения продаж')): ?>
                                        <a href="#"><?=$price_item['supplier_id']?></a>
                                    <?php else:?>
                                        <? if(ORM::factory('Permission')->checkPermission('supplier_information')) : ?>
                                            <a href="#" class="popover_link" rel="popover" data-placement="bottom" data-content="<?=addcslashes($price_item['supplier_name']."<br>".$price_item['supplier_name'], '"')?>"><?=$price_item['supplier_name']?></a>
                                        <?php else:?>
                                            <a href="#" class="popover_link" rel="popover" data-placement="bottom" data-content=""><?=$price_item['supplier_name']?></a>
                                        <?php endif;?>
                                    <?php endif;?>
                                </td>
                            <?php endif;?>
							<td>
								<? if($price_item['price_id']): ?>
									<a class="btn btn-mini" href="<?=URL::site('admin/orders/add_by_price_id?priceitem_id='.$price_item['price_id'].$discount_str);?>"><i class="icon-shopping-cart"></i> Добавить в заказ</a>
								<? else: ?>
									---
								<? endif; ?>
							</td>
							<td>
								<?php if ($flag) { ?>
									<span class="label label-success"><?= $original_crosses['flags']['quicle']?></span><br>
									<span class="label label-warning"><?= $original_crosses['flags']['midle']?></span><br>
									<span class="label label-danger"><?= $original_crosses['flags']['slow']?></span>
								<?php } ?>
							</td>
							<td>
								<?php if ($flag AND count($original_crosses['priceitems'])>1) { ?>
									<button class="btn btn-mini btn-primary show-more" type="button">Еще >>></button>
								<?php } ?>
							</td>
						</tr>
						<?php $flag = false; ?>
						<?php $count++; ?>
					<?php endforeach; ?>
				<?php endforeach; ?>

			</table>


			<!-- Modal -->
			<div id="orderitem_log" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3>Приблизительное время ожидания</h3>
				</div>
				<div class="modal-body">
				</div>
			</div>

		<?php endif; ?>


		<?php if (count($final_crosses_analog)>0):?>
			<b>Аналоги заменители</b>
			<table class="table table-striped table-bordered find-table">
				<tr>
					<th>Производитель</th>
					<th>Артикул</th>
					<th>Наименование</th>
					<? if(ORM::factory('Permission')->checkPermission('find_show_purchase')) { ?>
						<th class="purchase-column" style="<?=$hide_purchase ? "display: none;" : "" ?>">Цена закупки</th>
					<?php } ?>
					<? if(ORM::factory('Permission')->checkPermission('find_show_sale')) { ?>
						<th>Цена продажи</th>
					<?php } ?>
					<th>Кол-во</th>
					<th>Ожидание (дней)</th>
					<th>Примечания</th>
					<? if(ORM::factory('Permission')->checkPermission('find_show_supplier') && Auth::instance()->get_user()->id != 171) { ?>
						<th>Поставщик</th>
					<?php } ?>
					<th></th>
					<th></th>
					<th></th>
				</tr>
				<?php $flag = true; ?>
				<?php $count = 1; $unique_brand = []; ?>
				<?php foreach ($final_crosses_analog as $original_crosses): ?>
					<?php $flag = true; ?>
					<?php $count = 1; ?>
					<?php foreach($original_crosses['priceitems'] as $price_item) : ?>
						<?php if($price_item['price_start'] == 0 OR $price_item['amount'] < 0) continue; ?>
						<tr class="main-row <?=count($original_crosses['priceitems']) == $count ? "last" : ""?><?=($price_item['delivery'] == 1 ? " green-row" : "")?><?=(!$flag ? " must-hide" : " non-hide")?>" <?=($flag ? 'data-main="1"' : '')?>>
							<?php if($flag): ?>
								<td><b><?=$price_item['brand_long'] ?></b><br><?=$price_item['country'] ?></td>
								<td><?=$price_item['article_long'] ?><br><?php if(!empty($price_item['images'])){ ?><img style="width: 50px;" src="<?= URL::base(); ?>image/tecdoc_images<?=$price_item['images'] ?>" ><? } ?></td>
								<td>
									<?=Article::shorten_string($price_item['name'], 3)?><br>
									<?= $price_item['tecdoc_id'];  ?>
									<?php if (!empty($price_item['tecdoc_id'])) : ?>
										<?php $tecodc_id = $price_item['tecdoc_id']; ?>
										<a href="#position_log_<?=$tecodc_id;?>" role="button" class="btn btn-mini popover_link" data-content="Информация о запчасти" data-toggle="modal"><i class="icon-info-sign"></i> Инфо</a>
										<div id="position_log_<?=$tecodc_id;?>" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
												<h3>Info</h3>
											</div>
											<div class="modal-body">
												<ul class="nav nav-tabs">
													<li class="active"><a data-toggle="tab" href="#img_<?=$tecodc_id?>">Изображение</a></li>
													<li><a data-toggle="tab" href="#appl_<?=$tecodc_id?>">Характеристики</a></li>
													<li><a data-toggle="tab" href="#criter_<?=$tecodc_id?>">Применяемость</a></li>
												</ul>
												<div class="tab-content">
													<div id="img_<?=$tecodc_id?>" class="tab-pane fade in active">
														<img src="<?= URL::base(); ?>image/tecdoc_images<?= $price_item['images'] ?>">
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
									<?php endif?>

								</td>
							<?php else: ?>
								<td class="no-border"></td>
								<td class="no-border"></td>
								<td class="no-border"></td>
							<?php endif; ?>
							<? if(ORM::factory('Permission')->checkPermission('find_show_purchase')) { ?>
								<td class="purchase-column" style="<?=$hide_purchase ? "display: none;" : "" ?>">
									<?=round($price_item['price_start'],2)?> грн.
									<!--									--><?//=(isset($price_item->weight) ? "<br/> Вес: $price_item->weight кг": '')?>
								</td>
							<?php } ?>
							<? if(ORM::factory('Permission')->checkPermission('find_show_sale')) { ?>
								<td>
									<?=round(Article::get_price_for_client_by_namber($price_item['price_start'], $discount_id), 2) ;?> грн.
									<!--									--><?//=(isset($price_item->volume) AND $price_item->volume) ? "<span title='Возможна дополнительная плата за объем' style='cursor: pointer'><i class=\"icon-plane\"></i></span>": ''?>
								</td>
							<?php } ?>
							<td><?=$price_item['amount']?> </td>
							<td><?php
								if($price_item['delivery'] >= 5)
								{
									echo '<a href="#orderitem_log" role="button" class="popover_link" data-content="Приблизительное время ожидания" data-toggle="modal">~ '.$price_item['delivery'].'</a>';
								}
								else{
									echo $price_item['delivery'];
								}?>

								<?php
								if($price_item['delivery'] >= 3)
								{
									echo '<p style="color: red; font-size: 10px; line-height: 10px">заказ товара при полной оплате</p>';
								}?>
							</td>
							<td>
								<?php if($price_item['supplier_id']==38)
								{
									if($price_item['return_flag'] == 'Y')
									{
										echo "Существует возможность возврата";
									}
									else
									{
										echo "Нет возвратов";
									}
								}
								else
								{
									echo $price_item['notice'];
								}?>
							</td>
                            <? if(ORM::factory('Permission')->checkPermission('find_show_supplier') && Auth::instance()->get_user()->id != 171) : ?>
                                <td>
                                    <?php if(ORM::factory('Permission')->checkRole('manager') || ORM::factory('Permission')->checkRole('Руководитель отделения продаж')): ?>
                                        <a href="#"><?=$price_item['supplier_id']?></a>
                                    <?php else:?>
                                        <? if(ORM::factory('Permission')->checkPermission('supplier_information')) : ?>
                                            <a href="#" class="popover_link" rel="popover" data-placement="bottom" data-content="<?=addcslashes($price_item['supplier_name']."<br>".$price_item['supplier_name'], '"')?>"><?=$price_item['supplier_name']?></a>
                                        <?php else:?>
                                            <a href="#" class="popover_link" rel="popover" data-placement="bottom" data-content=""><?=$price_item['supplier_name']?></a>
                                        <?php endif;?>
                                    <?php endif;?>
                                </td>
                            <?php endif;?>
							<td>
								<? if($price_item['price_id']): ?>
									<a class="btn btn-mini" href="<?=URL::site('admin/orders/add_by_price_id?priceitem_id='.$price_item['price_id'].$discount_str);?>"><i class="icon-shopping-cart"></i> Добавить в заказ</a>
								<? else: ?>
									---
								<? endif; ?>
							</td>
							<td>
								<?php if ($flag) { ?>
									<span class="label label-success"><?= $original_crosses['flags']['quicle']?></span><br>
									<span class="label label-warning"><?= $original_crosses['flags']['midle']?></span><br>
									<span class="label label-danger"><?= $original_crosses['flags']['slow']?></span>
								<?php } ?>
							</td>
							<td>
								<?php if ($flag AND count($original_crosses['priceitems'])>1) { ?>
									<button class="btn btn-mini btn-primary show-more" type="button">Еще >>></button>
								<?php } ?>
							</td>
						</tr>
						<?php $flag = false; ?>
						<?php $count++; ?>
					<?php endforeach; ?>
				<?php endforeach; ?>

			</table>


			<!-- Modal -->
			<div id="orderitem_log" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3>Приблизительное время ожидания</h3>
				</div>
				<div class="modal-body">
				</div>
			</div>

		<?php endif; ?>

	<?php endif; ?>
</div>