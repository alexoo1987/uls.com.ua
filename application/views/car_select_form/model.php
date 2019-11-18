<?php if(empty($items)): ?>
	<span style="bold block">Модели для данного года и марки отсутствуют</span>
<?php else: ?>
	<li>
		<ul class="select_car_model">
			<?php foreach ($items AS $key => $item){ ?>
				<li>
					<a href="#" class="select-creteria" data-info="<?=$item['id']?>"><?=str_replace(" ", "&nbsp;", $item['name'])?></a>
				</li>
			<?php } ?>
		</ul>
	</li>
<?php endif; ?>