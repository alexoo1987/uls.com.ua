<div class="col-md-12">
	<h1>Пополнение кошелька</h1>
</div>
<div class="col-md-12">
	<form class="row form-inline" method="post" action="<?=URL::site('liqpay/checkout');?>">
		<div class="col-md-5">
			<div class="form-group">
			    <label for="inputAmount">Сумма пополнения (грн.)</label>
			    <input type="integer" class="form-control" name="amount" id="inputAmount" aria-describedby="inputAmountHelp">
			</div>
		</div>
		<div class="col-md-5">
			<button type="submit" class="btn btn-primary my-1">Пополнить</button>
		</div>
	</form>
</div>