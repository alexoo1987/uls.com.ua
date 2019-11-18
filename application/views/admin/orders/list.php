<script>
	var to_archive_url = '<?=URL::site('admin/orders/to_archive');?>';
</script>
<?php //foreach($orders_states as $state): ?>
<!--	<span class="badge badge-success">-->
<!--						--><?php //if(ORM::factory('Permission')->checkPermission('show_ready_orders') AND ( ORM::factory('Permission')->checkRole('Владелец') OR ORM::factory('Permission')->checkRole('Програмист') )){
//							echo ORM::factory('Order')->and_where_open()->where('ready_order', '=', 1)->or_where('ready_order', '=', '2')->and_where_close()->and_where('state', '=', $state->id)->count_all();
//						}
//						elseif(ORM::factory('Permission')->checkPermission('show_ready_orders') AND (ORM::factory('Permission')->checkRole('Старший Менеджер') OR ORM::factory('Permission')->checkRole('Менеджер') OR ORM::factory('Permission')->checkRole('Руководитель отделения продаж'))){
//							echo ORM::factory('Order')->where('ready_order', '=', 1)->and_where('state', '=', $state->id)->count_all();
//						}
//						elseif(ORM::factory('Permission')->checkPermission('show_my_ready_oredrs') AND ORM::factory('Permission')->checkRole('Менеджер')){
//							echo ORM::factory('Order')->where('ready_order', '=', 1)->and_where('state', '=', $state->id)->and_where('manager_id', '=', Auth::instance()->get_user()->id)->count_all();
//						}
//						elseif(ORM::factory('Permission')->checkPermission('show_ready_orders') AND (ORM::factory('Permission')->checkRole('Руководитель склада') OR ORM::factory('Permission')->checkRole('Закупка'))){
//							echo ORM::factory('Order')->where('ready_order', '=', 2)->and_where('state', '=', $state->id)->count_all();
//						}
//						?>
<!--					</span>-->
<?php //endforeach; ?>
<?php $is_buyer = ORM::factory('Permission')->checkRole('Закупщик');?>
<?php $page = isset($_GET['page']) ? $_GET['page'] : 1;?>
<div class="container">
	<div class="well col-md-12" style="float: right">
		<h4>Позиции заказов</h4>
		<div style="float: left; width: 200px;">
			<?php $count = 0; ?>
			<?php foreach($states as $state): ?>
			<?php if ($state->id == 1 AND $is_buyer) continue;?>
			<p>
				<a href="<?= URL::site('admin/orders/items'); ?>?state_id=<?= $state->id ?>">
					<img src="<?= URL::base() ?>media/img/states/<?= $state->img ?>" title="<?= $state->description ?>"/>
					<?= $state->name ?>
					<?php
					$orderitems = ORM::factory('Orderitem')
						->with('order')
						->where('state_id', '=', $state->id)
						->and_where('order.archive', '=', 0);
					if (ORM::factory('Permission')->checkRole('manager'))
						$orderitems->and_where('order.manager_id', '=', Auth::instance()->get_user()->id);
					?>
					(<?= $orderitems->count_all() ?>)
				</a>
				<?php if (in_array($state->text_id, array('withdrawal', 'not_available'))) { ?>
					<a href="<?= URL::site('admin/orders/items'); ?>?state_id=<?= $state->id ?>&confirmed=0" title="Неподтвержденные позиции">

						<?php $orderitems = ORM::factory('Orderitem')->reset(false)
							->with('order')
							->where('state_id', '=', $state->id)
							->and_where('order.archive', '=', 0)
							->and_where('confirmed', '=', 0);
						if (ORM::factory('Permission')->checkRole('manager'))
							$orderitems->and_where('order.manager_id', '=', Auth::instance()->get_user()->id);
						if ($orderitems->count_all() != 0) { ?>
							<span class="badge badge-important"><?= $orderitems->count_all() ?></span>
						<?php } ?>
					</a>

				<?php } ?>
			</p>
			<?php $count++; ?>
			<?php if($count > count($states) / 2): ?>
			<?php $count=0; ?>
		</div>
		<div style="float: left; width: 200px;">
			<?php endif; ?>
			<?php endforeach; ?>
			<?/*<p>
			<?php
				$orderitems = ORM::factory('Orderitem')->get_greater_than_delivered();
			?>
			Задержка поставки: <?=$orderitems->count_all()?>
		</p>*/?>
		</div>
	</div>
	<div class="well" style="float: right">
		<h4>Заказы</h4>
		<div style="float: right; width: 100px;">
			<?php foreach($orders_states as $state): ?>
				<p>
					<?php if(stristr($_SERVER['REQUEST_URI'], '?') === FALSE): ?>
					<a href="<?=URL::site('admin/orders');?>?state=<?=$state->id?>">
						<?php elseif ((stristr($_SERVER['REQUEST_URI'], '?state') === FALSE) AND (stristr($_SERVER['REQUEST_URI'], '&state') === FALSE)): ?>
						<a href="<?=$_SERVER['REQUEST_URI'];?>&state=<?=$state->id?>">
							<?php elseif (stristr($_SERVER['REQUEST_URI'], '?state') === TRUE): ?>
							<a href="<?=URL::site('admin/orders');?>?state=<?=$state->id?>">
								<?php elseif (stristr($_SERVER['REQUEST_URI'], '&state=1') == TRUE): ?>
								<a href="<?= str_replace("state=1", "state=".$state->id, $_SERVER['REQUEST_URI'])?>">
									<?php elseif (stristr($_SERVER['REQUEST_URI'], '&state=2') == TRUE): ?>
									<a href="<?= str_replace("state=2", "state=".$state->id, $_SERVER['REQUEST_URI'])?>">
										<?php elseif (stristr($_SERVER['REQUEST_URI'], '&state=3') == TRUE): ?>
										<a href="<?= str_replace("state=3", "state=".$state->id, $_SERVER['REQUEST_URI'])?>">
											<?php else: ?>
											<a href="<?=URL::site('admin/orders');?>?state=<?=$state->id?>">
												<?php endif; ?>
												<?=$state->name?>
												<?php
												$orders_temp = ORM::factory('Order')
													->where('state', '=', $state->id)
													->and_where('archive', '=', 0);
												if (ORM::factory('Permission')->checkRole('manager'))
													$orders_temp = $orders_temp->and_where('manager_id', '=', Auth::instance()->get_user()->id);

												$orders_temp = $orders_temp->find_all()->as_array();
												$orders_count = count($orders_temp);

												foreach ($orders_temp AS $order_temp){

													if (ORM::factory('Orderitem')
															->where('order_id', '=', $order_temp->id)
															->and_where('state_id', 'IN', array(1, 5))
															->count_all() ==
														$order_temp->orderitems->count_all())
														$orders_count--;
												}
												?>
												(<?=$orders_count?>)
												<a href="/admin/orders?state=<?=$state->id?>&ready_order=2">
													<?php if(ORM::factory('Permission')->checkPermission('show_ready_orders') OR ORM::factory('Permission')->checkPermission('show_my_ready_oredrs')): ?>
														<span class="badge badge-success">
															<?php
															$count = ORM::factory('Order')->where('state', '=', $state->id);
															if(ORM::factory('Permission')->checkPermission('show_ready_orders'))
															{
																$count = $count->and_where('ready_order', '=', 2);
															}

															if(ORM::factory('Permission')->checkPermission('show_my_ready_oredrs'))
															{
																$count = $count->and_where('ready_order', '=', 2);
																$count = $count->and_where('manager_id', '=', Auth::instance()->get_user()->id);
															}
															echo $count->count_all();
															?>
														</span>
													<?php endif; ?>
												</a>
												<a href="/admin/orders?state=<?=$state->id?>&ready_order=1">
													<?php if(ORM::factory('Permission')->checkPermission('packaging')): ?>
														<span class="badge badge-error">
															<?php
															$count = ORM::factory('Order')->where('state', '=', $state->id)->and_where('ready_order', '=', 1);
															echo $count->count_all();
															?>
														</span>
													<?php endif; ?>
												</a>
											</a>
				</p>
			<?php endforeach; ?>
			<?php if(ORM::factory('Permission')->checkPermission('show_ready_orders') OR ORM::factory('Permission')->checkPermission('show_my_ready_oredrs')): ?>
				<p><a href="/admin/orders?ready_order=2">Готовые/упакованые заказы
						<span class="badge badge-success">
							<?php
							$count = ORM::factory('Order');

							if(ORM::factory('Permission')->checkPermission('show_ready_orders'))
							{
								$count = $count->and_where('ready_order', '=', 2);
							}

							if(ORM::factory('Permission')->checkPermission('show_my_ready_oredrs'))
							{
								$count = $count->and_where('ready_order', '=', 2);
								$count = $count->and_where('manager_id', '=', Auth::instance()->get_user()->id);
							}
							echo $count->count_all();
							?>
						</span>
					</a>
				</p>
			<?php endif; ?>
			<?php if(ORM::factory('Permission')->checkPermission('packaging')): ?>
				<p><a href="/admin/orders?ready_order=1">Ждут упаковки
						<span class="badge badge-error">
							<?php
							$count = ORM::factory('Order');
							if(ORM::factory('Permission')->checkPermission('packaging'))
							{
								$count = $count->and_where('ready_order', '=', 1);
							}
							echo $count->count_all();
							?>
						</span>
					</a>
				</p>
			<?php endif; ?>
		</div>
	</div>
	<?php
	// показывать блок возвратов
	if (ORM::factory('Permission')->checkPermission('show_button_confirmation_return')): ?>
		<div class="well" style="float: right">
			<h4>Статусы заказов</h4>
			<div style="width: 100px;">
				<?php $delivery_status = array(
					0 => 'Можно уведомить',
					1 => 'Уведомление о возврате',
					2 => 'В процессе возврата',
					3 => 'Возвращено',
				);?>
				<?php foreach($delivery_status as $status => $status_name):
					if($status == 1):?>
						<p>
							<a href="<?=URL::site('admin/orders');?>?delivery_status=<?=$status?>">
								<?=$status_name?>
								<?php

								//показывать все

								$orders_temp = ORM::factory('Order')
									->where('delivery_status', '=', $status)
									->and_where('archive', '=', 0);

								$orders_count = $orders_temp->find_all()->count();
								?>
								<span class="badge badge-important"><?=$orders_count?></span>
							</a>
						</p>
					<?php elseif ($status == 2): ?>
						<p>
							<a href="<?=URL::site('admin/orders');?>?delivery_status=<?=$status?>">
								<?=$status_name?>
								<?php
								//показывать только свои возвраты
								if (ORM::factory('Permission')->checkPermission('show_only_own_return'))
								{
									$orders_temp = ORM::factory('Order')
										->where('delivery_status', '=', $status)
										->and_where('id_purchasing_agent', '=', Auth::instance()->get_user()->id)
										->and_where('archive', '=', 0);
									$orders_count = $orders_temp->find_all()->count();
								}
								//показывать все
								else
								{
									$orders_temp = ORM::factory('Order')
										->where('delivery_status', '=', $status)
										->and_where('archive', '=', 0);
									$orders_count = $orders_temp->find_all()->count();
								}

								?>
								<span class="badge badge-important"><?=$orders_count?></span>
							</a>
						</p>
					<?php elseif ($status == 3): ?>

						<p>
							<a href="<?=URL::site('admin/orders');?>?delivery_status=<?=$status?>">
								<?=$status_name?>
								<?php
								//показывать только свои возвраты
								if (ORM::factory('Permission')->checkPermission('show_only_own_return'))
								{
									$orders_temp = ORM::factory('Order')
										->where('delivery_status', '=', $status)
										->and_where('id_purchasing_agent', '=', Auth::instance()->get_user()->id)
										->and_where('archive', '=', 0);
									$orders_count = $orders_temp->find_all()->count();
								}
								//показывать все
								else
								{
									$orders_temp = ORM::factory('Order')
										->where('delivery_status', '=', $status)
										->and_where('archive', '=', 0);
									$orders_count = $orders_temp->find_all()->count();
								}

								?>
								(<?=$orders_count?>)
							</a>
						</p>
					<?php endif; endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
	<?= Form::open('', array('class' => 'form-horizontal', 'method' => 'get')); ?>
	Дата от <?= Form::input('date_from', HTML::chars(Arr::get($filters, 'date_from')), array('class' => 'datepicker')); ?> до <?= Form::input('date_to', HTML::chars(Arr::get($filters, 'date_to')), array('class' => 'datepicker')); ?><br />
	<? if(ORM::factory('Permission')->checkPermission('orders_show_manager') AND !ORM::factory('Permission')->checkRole('manager')) { ?>
		Менеджер <?= Form::select('manager_id', $managers, Arr::get($filters, 'manager_id')); ?><br />
	<?php } ?>
	# заказа <?= Form::input('order_id', HTML::chars(Arr::get($filters, 'order_id'))); ?><br />
	TTN <?= Form::input('ttn', HTML::chars(Arr::get($filters, 'ttn'))); ?><br />
	Архив <?= Form::select('archive', array('all' => 'Все', '0' => 'Нет', '1' => 'Да'), Arr::get($filters, 'archive')); ?><br />
	Фамилия клиента <?= Form::input('client', HTML::chars(Arr::get($filters, 'client'))); ?><br />
	Телефон клиента <?= Form::input('phone', HTML::chars(Arr::get($filters, 'phone')), array('class' => 'bfh-phone', 'data-format' => '(ddd)ddd-dd-dd', 'data-number' => preg_replace('/[^0-9]/', '', HTML::chars(Arr::get($filters, 'phone'))), 'validate' => 'required|phone')); ?><br />
	Готовые заказы <?= Form::checkbox('ready', 1, (Arr::get($filters, 'ready') == 1)); ?><br />
	TTN <?= Form::checkbox('ttn_is', 1, (Arr::get($filters, 'ttn_is') == 1)); ?><br />
	Онлайн <?= Form::select('online', array('all' => 'Все', '0' => 'Нет', '1' => 'Да'), Arr::get($filters, 'online')); ?><br />

	<?= Form::submit('', 'Применить фильтр', array('class' => 'btn btn-primary')); ?>
	<?= Form::close(); ?>
	<a href="<?=URL::site('admin/orders');?>" class="btn btn-primary"><i class="icon-white icon-refresh"></i> Сброс</a><br /><br />

	<? if(ORM::factory('Permission')->checkPermission('orders_edit_archive')) { ?>
		<a href="#" class="btn btn-primary" id="move_to_archive"><i class="icon-white icon-arrow-right"></i> В архив</a>
	<?php } ?>
	<br /><br />
	<?php if (ORM::factory('Permission')->checkPermission('synchronization_states')): ?>
		<a href="<?=URL::site('admin/orders/synchronization');?>" class="btn btn-primary"><i class="icon-white icon-refresh"></i> Синхронизировать статусы с техномиром</a><br /><br />
	<?php endif ?>



	<table class="table table-striped table-bordered">
		<thead>
		<tr>
			<th>№</th>
			<th><input type="checkbox" id="select_all" /></th>
			<th><?=Utils::order_by($order_by, "id", "№ заказа")?></th>
			<th><?=Utils::order_by($order_by, "date_time", "Дата")?></th>
			<th>Клиент</th>
			<th>Состояние</th>
			<th>
				<? if(ORM::factory('Permission')->checkPermission('orders_show_purchase')) { ?>
					Закупка/
				<?php } ?>
				<? if(ORM::factory('Permission')->checkPermission('orders_show_sale')) { ?>
					Продажа
				<?php } ?>
			</th>
			<th>Доставка</th>
			<? if(ORM::factory('Permission')->checkPermission('orders_show_manager')) { ?>
				<th><?=Utils::order_by($order_by, "manager_id", "Менеджер")?></th>
			<?php } ?>
			<th>Дополнительно</th>
			<th>Баланс общ/заказ</th>
			<th>Архив</th>
			<?php if (!empty($_GET['delivery_status'])) :?>
				<th>Ответсвенный <br>за возврат</th>
				<th>Принял уведомление <br>о возврате</th>
			<?php endif; ?>
			<th ></th>
		</tr>
		</thead>
		<tbody>

		<?php $i=0; foreach($orders as $order) : $i++;?>
			<?php $is_state_1 = ORM::factory('Orderitem')->where('order_id', '=', $order->id)->and_where('state_id', '=', 1)->count_all(); ?>
			<?php if ($is_state_1 == $order->orderitems->count_all() AND $is_buyer) continue;?>
			<?php
			if (!empty($_GET['state'])) {
				if (ORM::factory('Orderitem')
						->where('order_id', '=', $order->id)
						->and_where('state_id', 'IN', array(1, 5))
						->count_all() ==
					$order->orderitems->count_all()){
					$i--;
					continue;
				}
			}
			?>

			<tr class="<?=($order->ready ? 'ready_order' : '')?>">
				<td><?=$i + ($page-1) * 30;?></td>
				<td><input type="checkbox" class="order_checkbox" data-id="<?=$order->id?>" /></td>
				<td><a href="<?=URL::site('admin/orders/items/'.$order->id);?>"><?=$order->get_order_number()?></a>
					<!-- кнопка Уведомить о возврате-->
					<? if(ORM::factory('Permission')->checkPermission('show_button_inform_return')){ ?>
						<? if($order->delivery_status == 0) { ?>
							<a style="margin-top: 20px;" href="<?=URL::site('admin/orders/return_order_inform/'.$order->id);?>" role="button" class="btn btn-mini" data-toggle="modal">Уведомить о возврате</a>
						<?php }elseif(($order->delivery_status == 1)){?>
							<br><span style="margin-top: 20px;"  class="btn_info_admin yellow"> Уведомление о возврате отправлено</span>
						<?php }elseif(($order->delivery_status == 2)){?>
							<br><span style="margin-top: 20px;"  class="btn_info_admin yellow"> Уведомление принято</span>
						<?php }elseif(($order->delivery_status == 3)){?>
							<br><span style="margin-top: 20px;" class="btn_info_admin red"> Заказ возвращен</span>
						<?php } ?>
					<?php } ?>

					<!-- кнопка Принять уведомление о возврате-->
					<?php
					if((ORM::factory('Permission')->checkPermission('show_button_confirmation_return'))&&($order->delivery_status == 1)) { ?>
						<br><a style="margin-top: 20px;"  href="<?=URL::site('admin/orders/confirmation_order_inform/'.$order->id);?>" role="button" class="btn btn-mini" data-toggle="modal">Ознакомлен с возвратом</a>
					<?php }?>

					<!-- кнопки про готовность-->
					<!--					<br><br>-->
					<!--					--><?// if(ORM::factory('Permission')->checkPermission('show_button_inform_return')){ ?>
					<!--						--><?// if($order->ready_order == 1) { ?>
					<!--							<a style="margin-top: 20px;" href="--><?//=URL::site('admin/orders/ready_order/'.$order->id);?><!--" role="button" class="btn btn-mini" data-toggle="modal">Ознакомлен с готовностью ---><?php //if ($order->state == 1){echo " ДОСТАВЛЯЕМ"; }elseif($order->state == 2){echo " ОТПРАВЛЯЕМ";}elseif($order->state == 3){echo " ОТГРУЖАЕМ";} ?><!--</a>-->
					<!--						--><?php //}elseif(($order->ready_order == 2)){?>
					<!--							<span style="margin-top: 20px;"  class="btn_info_admin yellow"> Уведомление о готовности принято</span>-->
					<!--						--><?php //} ?>
					<!--					--><?php //} ?>



				</td>
				<td><?php $d = new DateTime($order->date_time); ?><?=$d->format('d.m.Y H:i:s')?></td>
				<td><?=$order->client->name?> <?=$order->client->surname?><br><?=$order->client->phone?></td>
				<td>
					<? foreach($order->orderitems->find_all()->as_array() as $orderitem) { ?>
						<?php if ($orderitem->state->id == 1 AND $is_buyer) continue;?>
						<img src="<?=URL::base()?>media/img/states/<?=$orderitem->state->img?>" title="<?=$orderitem->state->name?>" />
					<? } ?>

					<?php
					if(ORM::factory('Permission')->checkPermission('give_all_order') AND ORM::factory('Permission')->checkPermission('orders_edit_state') AND (ORM::factory('Permission')->checkRole('Руководитель склада') OR ORM::factory('Permission')->checkRole('Менеджер по Выдаче') OR ORM::factory('Permission')->checkRole('Закупка') OR ORM::factory('Permission')->checkRole('Приемщик, Сортировщик') OR ORM::factory('Permission')->checkRole('Закупка') OR ORM::factory('Permission')->checkRole('Владелец') OR ORM::factory('Permission')->checkRole('Програмист') OR ORM::factory('Permission')->checkRole('packer') )){
						?>
						<?php if(ORM::factory('Permission')->checkPermission('give_all_order') AND ($order->ready_order == 1 OR $order->ready_order == 2) AND $order->delivery_method_id != 6){ echo "<br><br><a href='/admin/orders/give_all_position?order_id=$order->id' class='btn btn-mini'>Выдано</a>"; }
						elseif (ORM::factory('Permission')->checkPermission('give_all_order') AND ($order->ready_order == 1 OR $order->ready_order == 2) AND $order->delivery_method_id == 6) {echo "<br><br><a href='/admin/orders/delivery_all_position?order_id=$order->id' class='btn btn-mini'>Курьерская доставка</a> <br><br><a href='/admin/orders/give_all_position?order_id=$order->id' class='btn btn-mini'>Выдано</a>";}

						if(ORM::factory('Permission')->checkPermission('give_all_order') AND $order->ready_order == 1 AND $order->delivery_method_id == 3){ echo "<br><br><a href='/admin/orders/packaging_all_position?order_id=$order->id' class='btn btn-mini'>Упаковано</a>"; }

						?>
					<?php } ?>

				</td>
				<td>
					<?
					$purchase = 0;
					$sale = 0;
					foreach($order->orderitems->find_all()->as_array() as $orderitem) {
						$purchase += $orderitem->purchase_per_unit * $orderitem->amount;
						$sale += $orderitem->sale_per_unit * $orderitem->amount;
					} ?>
					<? if(ORM::factory('Permission')->checkPermission('orders_show_purchase')) { ?>
						<?=$purchase?> грн. /
					<?php } ?>
					<? if(ORM::factory('Permission')->checkPermission('orders_show_sale')) { ?>
						<?=$sale?> грн.
					<?php } ?>
				</td>
				<td>
					<?=(!empty($order->delivery_method) ? "<b>Метод доставки:</b> ".$order->delivery_method->name."<br />" : "")?>
					<?php if($order->delivery_method->id == 3): ?>
						<b>Детали доставки: </b><?= $order->getDeliveryNpDetails()?>
					<?php else: ?>
						<?=(!empty($order->delivery_address) ? "<b>Детали доставки:</b> ".$order->delivery_address."<br />" : "")?>
					<?php endif; ?>

				</td>
				<? if(ORM::factory('Permission')->checkPermission('orders_show_manager')) { ?>
					<td>
						<?=$order->manager->surname?>
					</td>
				<?php } ?>
				<td>
					<?=(!empty($order->manager_comment) ? "<b>Комментарий менеджера:</b> ".$order->manager_comment."<br />" : "")?>
					<?php if(isset($order->confirmation)) : ?>
						<?=($order->confirmation==1 ? "<b>Подтверждение</b>: необходимо<br />" : "<b>Подтверждение</b>: ненужно<br />")?>
					<?php endif ?>
					<?=(!empty($order->client_comment) ? "<b>Комментарий клиента:</b> ".$order->client_comment."<br />" : "")?>
					<?=(!empty($order->client->comment) ? "<b>Комментарий к клиенту:</b> ".$order->client->comment."<br />" : "")?>
					<b>Онлайн заказ:</b><?=($order->online == 1 ? "Да" : "Нет")?><br />
					<?=(!empty($order->ttn) ? "<b>ТТН:</b> ".$order->ttn."<br />" : "")?>
				</td>
				<td style="">
					<? $balance = $order->get_balance(); ?>
					<span style="<?=(($clients_on_page[$order->client_id]['active_balance'] < 0) ? "color: #FF6666 !important;" : "color: #00AA00 !important;")?> font-weight: bold;">
						<?=$clients_on_page[$order->client_id]['active_balance']?>
					</span> / <span style="<?=(($balance['balance'] < 0) ? "color: #FF6666 !important;" : "color: #00AA00 !important;")?> font-weight: bold;"><?=$balance['balance']?></span>
				</td>
				<td><?=($order->archive == 1 ? "Да" : "Нет")?></td>
				<?php if (!empty($_GET['delivery_status'])) :?>
					<td><?=$order->agent->surname; ?></td>
					<td><?= $order->date_time_return_confim; ?></td>
				<?php endif; ?>
				<td>
					<?php if ((ORM::factory('Permission')->checkPermission('orders_show_manager')) AND ($is_state_1) AND (!$is_buyer)) { ?>
						<a title="Отправить в закупку" alt="Отправить в закупку" data-order_id="<?=$order->id;?>" class="btn btn-mini to_purchase"><i class="icon-tasks"></i></a>
					<?php } ?>
					<a class="btn btn-mini" href="<?=URL::site('admin/orders/print/'.$order->id);?>"><i class="icon-print"></i></a>
					<a class="btn btn-mini" href="<?=URL::site('admin/orders/print_sticker/'.$order->id);?>"><i class="icon-tag"></i></a>
					<a class="btn btn-mini" href="<?=URL::site('admin/orders/edit/'.$order->id);?>"><i class="icon-edit"></i></a>
					<? if(ORM::factory('Permission')->checkPermission('orders_delete')) { ?>
						<a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/orders/delete/'.$order->id);?>"><i class="icon-remove"></i></a>
					<?php } ?>
					<a href="#sendTTN" title="Отправить клиенту ТТН" role="button" class="btn btn-mini"
					   data-toggle="modal" data-phone="<?= $order->client->phone ?>"
					   data-method="<?= $order->delivery_method->name ?>"
					   data-order_id="<?= $order->id ?>"
					   data-ttn="<?= (!empty($order->ttn)) ? $order->ttn : "" ?>"><i class="icon-envelope"></i></a>
					<br/>
					<?php if($order->np_area_id AND $order->np_city_id AND $order->np_warehouse_id): ?>
						<?php $flag_position = 0;
						$order_items_array = [];
						$order_items_array = $order->orderitems->find_all()->as_array();
						foreach ($order_items_array as $position){
							if(($position->state_id == 3 AND $order->delivery_method_id != 3) OR ($position->state_id == 37 AND $order->delivery_method_id == 3))
							{
								$flag_position = 1;
							}
						}?>
						<?php if($flag_position): ?>
							<a href="/admin/orders/create_express_for_order/<?=$order->id ?>" title="Накладная" class="btn btn-mini"><i class="icon-file"></i></a>
						<?php endif; ?>
					<?php endif; ?>

				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<?=$pagination?>

	<!-- TTN modal -->
	<div id="sendTTN" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3>Отправка ТТН клиенту по СМС</h3>
		</div>
		<div class="modal-body">
			<input type="text" class="input-block-level inputTTN" placeholder="Укажите номер ТТН" required>
			<input type="hidden" class="phone">
			<input type="hidden" class="method">
			<input type="hidden" class="order_id">
			<div class="alert alert-info">
				Указанный номер товарно транспортной накладной будет отправлен клиенту с помощью СМС
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true">Отмена</button>
			<button class="btn btn-primary submitTTN" data-dismiss="modal" disabled="disabled">Отправить</button>
		</div>
	</div>
</div>