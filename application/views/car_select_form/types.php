<li>
	<ul class="select_car_modif">
		<?php foreach ($items AS $key => $item){ ?>
			<li>
				<a href="#" class="select-creteria" data-info="<?=$item['id']?>"><?=str_replace(" ", "&nbsp;", $item['name']).", ".$item['capacity_hp_from']." л.с."?></a>
			</li>
		<?php } ?>
	</ul>
</li>