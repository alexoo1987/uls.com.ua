<div class="container">
    <div class="row">
		<div class="span6 offset3">			
			<div class="progress progress-success">
				<div style="width: 0%;" class="bar"></div>
			</div>
			<span class="state"><span class="current">0</span>/<?=$lines_count?></span>
			<p>Директория для фото: <?= $date_time ?></p>
			
			<script>
				var current = 0;
				var lines_count = <?=$lines_count?>;
				var posr_addr = '<?=URL::site('admin/images/proccess')?>';
			</script>
		</div>
    </div>
</div>