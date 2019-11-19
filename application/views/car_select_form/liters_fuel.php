<li>
	<?php foreach($items as $eng_type=>$capacity_list): ?>
		<ul class="select_car_mod">
			<li><span style="bold"><?=$eng_type?></span></li>
			<?php sort($capacity_list); ?>
			<?php foreach($capacity_list as $row): ?>
				<li><a href="#" class="select-creteria" data-liters="<?=$row['name']?>" data-info="<?=$row['id']?>"><?php $liters = $row['name']/1000; $liters = round($liters,2,PHP_ROUND_HALF_UP);  echo round($liters,1,PHP_ROUND_HALF_UP);?></a>
			<?php endforeach; ?>
		</ul>
	<?php endforeach; ?>
</li>
