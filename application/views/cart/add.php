<?= Form::open($current_url, array('class' => 'form-horizontal cart-form', 'id' => 'validate_form', 'autocomplete' => 'off')); ?>
	<div class="modal-header">
		<button class="btn close" data-dismiss="modal">×</button>
		<span class="bold"><i class=""></i><span class="break"></span>Добавить в корзину</span>
	</div>
	<div class="modal-body">
		<?= Form::hidden('priceitem_id', HTML::chars(Arr::get($data, 'priceitem_id'))) ?>
		<?= Form::hidden('redirect_to', HTML::chars(Arr::get($data, 'redirect_to'))) ?>
		
		<div class="control-group">
			<div class="controls">
				<span class="bold">Артикул:</span> <?=$priceitem->part->article_long?><br>
				<span class="bold">Производитель:</span> <?=$priceitem->part->brand_long?><br>
				<span class="bold">Цена за шт.:</span> <?=$priceitem->get_price_for_client()?> грн.<br>
			</div>
		</div>
		<div class="control-group">
			<?= Form::label('qty', 'Количество', array('class' => 'control-label ')); ?>
			<div class="controls">
				<?= Form::input('qty', '1', array('data-format' => 'ddddd', 'validate' => 'required|number|min,1')); ?>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal">Отмена</button>
		<?= Form::submit('submit', 'Добавить в корзину', array('class' => 'btn btn-success')); ?>
	</div>
<?= Form::close(); ?>
