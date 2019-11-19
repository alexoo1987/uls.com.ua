<div class="container">
    <div class="row">
		<div class="span6 offset3">			
			<div class="progress progress-success">
				<div style="width: 0%;" class="bar"></div>
			</div>
			<span class="state"><span class="current">0</span>/<?=$lines_count?></span>
			<div id="price_link">
				<a href="<?=URL::site('uploads/eparts_price.zip')?>">Скачать прайсы</a>
			</div>
			
			<script>
				var current = 0;
				var lines_count = <?=$lines_count?>;
				var posr_addr = '<?=URL::site('admin/pricedownload/proccess')?>';
			</script>
		</div>
    </div>
</div>