<li>
	<ul class="select_car_type">
		<?php foreach ($items AS $key => $item){ ?>
			<li>
				<a href="#" class="select-creteria" data-info="<?=$item['id']?>"><?=str_replace(" ", "&nbsp;", $item['name'])?></a>
			</li>
		<?php } ?>
	</ul>
</li>