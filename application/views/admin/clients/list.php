<div class="container">
	<a class="btn btn-mini" href="<?=URL::site('admin/clients/add');?>"><i class="icon-plus"></i> Добавить</a><br /><br />
	
	<?= Form::open('', array('class' => 'form-horizontal', 'method' => 'get')); ?>
		<? if(ORM::factory('Permission')->checkPermission('clients_show_manager')) { ?>
		Менеджер <?= Form::select('manager_id', $managers, Arr::get($filters, 'manager_id')); ?><br />
		<?php } ?>

		Фамилия <?= Form::input('surname', HTML::chars(Arr::get($filters, 'surname'))); ?><br />

		Тип клиента <?= Form::select('client_type', [
			''                     => 'Все',
			Model_Client::TYPE_FIZ => 'Физическое лицо',
			Model_Client::TYPE_JUR => 'Юридическое лицо',
		], Arr::get($filters, 'client_type')); ?><br /><br />

		Участники акции <?= Form::checkbox('only_actions', 1, Arr::get($filters, 'only_actions') === '1'); ?><br /><br />

		<!--	<div class="control-group">-->
		<!--		--><?//= Form::label('phone', 'Телефон клиента', array('class' => 'control-label ')); ?>
		<!--		<div class="controls">-->
			Телефон клиента <?= Form::input('phone', HTML::chars(Arr::get($data, 'phone')), array('class' => 'bfh-phone', 'data-format' => '+38(ddd)ddd-dd-dd' , 'placeholder' => '+38', 'validate' => 'required|phone')); ?><br />
		<!--		</div>-->

		
		<?= Form::submit('', 'Применить фильтр', array('class' => 'btn btn-primary')); ?>
	<?= Form::close(); ?>
	<a href="<?=URL::site('admin/clients');?>" class="btn btn-primary"><i class="icon-white icon-refresh"></i> Сброс</a><br /><br />
	
	
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th class="filter-false">Тип клиента</th>
				<th>СТО</th>
				<th>Имя</th>
				<th>Фамилия</th>
				<th>Отчество</th>
				<th class="sorter-false">Телефон</th>
<!--				<th class="sorter-false filter-false">Доп. тел.</th>-->
				<th class="sorter-false filter-false">Email</th>
				<? if(ORM::factory('Permission')->checkPermission('clients_show_manager')) { ?>
				<th class="filter-select">Менеджер</th>
				<?php } ?>
<!--				<th class="sorter-false filter-false">Способ доставки</th>-->
<!--				<th class="sorter-false filter-false">Адрес доставки</th>-->
				<th class="filter-select">Уровень цен</th>
				<th class="filter-select">Коментарий</th>
				<th>Дата рег.</th>
				<th class="sorter-false filter-false"></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($clients as $client) : ?>
		<tr>
			<td><?=$client->get_client_type()?></td>
			<td><?=$client->is_service_station ? 'СТО' : 'Нет'?></td>
			<td><?=$client->name?></td>
			<td><?=$client->surname?></td>
			<td><?=$client->middlename?></td>
			<td><?=$client->phone?></td>
<!--			<td>--><?//=$client->additional_phone?><!--</td>-->
			<td><?=$client->email?></td>
			<? if(ORM::factory('Permission')->checkPermission('clients_show_manager')) { ?>
			<td><?=$client->manager->name." ".$client->manager->surname?></td>
			<?php } ?>
<!--			<td>--><?//=$client->delivery_method->name?><!--</td>-->
<!--			<td>--><?//=$client->delivery_address?><!--</td>-->
			<td><?=$client->discount->name?></td>
			<td><?=$client->comment?></td>
			<td><?php $d = new DateTime($client->registration_date); ?><?=$d->format('d.m.Y H:i:s')?></td>
			<td>
				<a class="btn btn-mini" href="<?=URL::site('admin/clientpayment/list');?>?client_id=<?=$client->id?>"><i class="icon-list-alt"></i> Баланс</a>
				<a class="btn btn-mini" href="<?=URL::site('admin/cars/index/'.$client->id);?>"><i class="icon-info-sign"></i> Автомобили</a>
				<a class="btn btn-mini" href="<?=URL::site('admin/clients/edit/'.$client->id);?>"><i class="icon-edit"></i> Редактировать</a>
				<?php if (ORM::factory('Permission')->checkRole('Владелец')):?>
				<a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/clients/delete/'.$client->id);?>"><i class="icon-remove"></i> Удалить</a>
				<?php endif;?>
			</td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?=$pagination?>
</div>