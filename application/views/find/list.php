<script type="text/javascript">
	var image_url = "<?=URL::site('katalog/get_images');?>";
</script>
<?php $current_url = URL::query(); ?>
<div class="gap"></div>
<!--<a type="button" style="margin: 0px AUTO;margin-bottom: 20px;" href="/findexpert/index--><?//= $current_url?><!--" class="btn btn-primary vin_code_long">Режим эксперта&nbsp;&nbsp;<span class="tooltips" tabindex="0"><i class="fa fa-info table-shopping-remove" style="font-size: 14px;width: 20px;height: 20px;line-height: 20px;"></i><span style="width: 300px;"> Более широкий <br> выбор</span></span></a>-->
	<?php
		if(count($parts) > 1) { ?>
		<span class="block widget-title-lg">Выберите нужный бренд</span>
		<table class="table1">
			<thead>
			<tr>
				<th>Бренд</th>
				<th>Артикул</th>
				<th>Описание</th>
			</tr>
			</thead>

			<?php foreach($parts as $part) : ?>
		<tr onclick="window.location = '<?=URL::site('find/index');?>?art=<?=$part['article']?>&brand=<?= urlencode($part['brand'])?>'">
			<td><?=$part['brand_long']?></td>
			<td><?=$part['article_long']?></td>
			<td><?=Article::shorten_string($part['name'], 3)?></td>
		</tr>
		<?php endforeach; ?>
	</table>
	<?php } elseif(count($parts) < 1 AND count($crosses) < 1) { ?>
		<p>По вашему запросу "<?=empty($_GET['brand'])?"":$_GET['brand']." "; ?><?=$_GET['art']?>" ничего не найдено</p>
	<?php } ?>

	<?php if(count($parts) == 1): ?>
		<ul class="nav nav-tabs">
			<h1 class="active">Искомый артикул</h1>
		</ul>
		<div class="row" data-gutter="15">
			<?php foreach($parts as $key => $price_item) :?>
				<?php if($price_item['price_start'] == 0) continue; ?>
				<div class="col-md-3">
<!--							--><?php //if(in_array( $price_item['article'], $top_orderitems)):?>
<!--                            <img class="find_line" src="/images/1.png">-->
<!--							--><?php //endif;?>

					<div class="product">
						<div class="product-img-wrap">
								<a class="product-link" href="<?= URL::site('katalog/article/'); ?>/<?= $price_item['id'] ?>">
								<?php if (!empty($price_item['images'])) :?>
								<img class="product-img-primary" src="<?= URL::base(); ?>image/tecdoc_images<?= $price_item['images'] ?>"
									 alt="" />

								<img class="product-img-alt" src="<?= URL::base(); ?>image/tecdoc_images<?= $price_item['images'] ?> "
									 alt=""/>

								<?php else: ?>
									<img class="product-img-primary" src="<?= URL::base(); ?>media/img/no-image.png"
										 alt="" itemprop="image"/>
									<img class="product-img-alt" src="<?= URL::base(); ?>media/img/no-image.png"
										 alt="" itemprop="image"/>
								<?php endif; ?>
								</a>
						</div>
						<div class="product-caption">
							<span class="block product-caption-title"><?=$price_item['brand_long']?> <?=$price_item['article_long']?> <?=Article::shorten_string($price_item['name'], 3)?></span>
							<div class="js_hide"><p>Производитель: <br><span  class="bold"><?=$price_item['brand_long']?></span></p>
							<p>Страна производителя: <br><span  class="bold"><?=$price_item['country']?></span></p>
							<p>Срок доставки: <span  class="bold">
									<?php
									if($price_item['delivery'] >= 5)
									{

										echo "<span class='tooltips' tabindex='0'>~ <span> Приблизительный срок доставки</span></span>".Helper_Custom::getDay($price_item['delivery']);
									}
									else{
										echo Helper_Custom::getDay($price_item['delivery']);
									}?> </span></p></div>

							<div class="product-caption-price">
								<?php if ($guest == true) : ?>
									<span class="product-caption-price-new"><span class="tooltips js_hide" tabindex="0"><i class="fa fa-info table-shopping-remove green" style="font-size: 14px;width: 20px;height: 20px;line-height: 20px;"></i><span style="width: 300px;">Цена при создании онлайн заказа</span></span>Цена: <?= round($price_item['price'], 0) ?> грн </span>
								<?php else: ?>
									<span class="product-caption-price-new">Цена: <?=round(Article::get_price_for_client_by_namber($price_item['price_start']), 0) ;?> грн</span>
								<?php endif ?>
							</div>

							<ul class="product-caption-feature-list js_hide">
								<li>
									<?php $price_id = $price_item['price_id'] ?>
									<?= (empty($price_item['amount']) ? "<span id ='number_item_$price_id'>В наличии</span><span class='tooltips' tabindex='0'>&nbsp;~ <span> Неопределенное количество</span></span>" : "Доступно:&nbsp;<span id='number_item_$price_id'>". $price_item['amount'] ."</span>&nbsp;шт") ?>
								</li>
							</ul>
						</div>
                        <?php if($readMoreButton['enable']->value == 1): ?>
                            <p style="text-align: center;">
                                <a class="btn btn-lg btn-primary" style="text-align: center;" href="<?= URL::site('katalog/article/'); ?>/<?= $price_item['id'] ?>"><?= $readMoreButton['text']->value ?></a>
                            </p>
                        <?php endif; ?>
						<p style="text-align: center;">
							<input id="qty_<?=$price_id?>" class="product-page-qty product-page-qty-input" type="hidden" value="1" />
							<a class="btn btn-lg btn-primary add-to-cart" data-href="<?= URL::site("cart/add"); ?>?price_id=<?= $price_id ?>" data-priceitem="<?= $price_id ?>" style="text-align: center;"><i class="fa fa-shopping-cart"></i><?= $buyTextButton->value ?></a>
						</p>

					</div>
				</div>
				<?php  endforeach;  ?>

		</div>
		<div class="clearfix"></div>
	<?php endif; ?>
<?php if(count($crosses) >= 1): ?>
	<ul class="nav nav-tabs">
		<h1 class="active">Аналоги</h1>
	</ul>
	<div class="row" data-gutter="15">
		<?php foreach($crosses as $key => $price_item) :?>
            <?php if($price_item['price_start'] == 0) continue; ?>
			<div class="col-md-3">
				<!--							--><?php //if(in_array( $price_item['article'], $top_orderitems)):?>
				<!--                            <img class="find_line" src="/images/1.png">-->
				<!--							--><?php //endif;?>

				<div class="product">
					<div class="product-img-wrap">

						<a class="product-link" href="<?= URL::site('katalog/article/'); ?>/<?= $price_item['part_id'] ?>">
							<?php if (!empty($price_item['images'])) :?>
								<img class="product-img-primary" src="<?= URL::base(); ?>image/tecdoc_images<?= $price_item['images'] ?>"
									 alt="" />

								<img class="product-img-alt" src="<?= URL::base(); ?>image/tecdoc_images<?= $price_item['images'] ?> "
									 alt=""/>

							<?php else: ?>
								<img class="product-img-primary" src="<?= URL::base(); ?>media/img/no-image.png"
									 alt="" itemprop="image"/>
								<img class="product-img-alt" src="<?= URL::base(); ?>media/img/no-image.png"
									 alt="" itemprop="image"/>
							<?php endif; ?>
						</a>
					</div>
					<div class="product-caption">
						<span class="block product-caption-title"><?=$price_item['brand_long']?> <?=$price_item['article_long']?> <?=Article::shorten_string($price_item['name'], 3)?></span>
						<div class="js_hide"><p>Производитель: <br><span  class="bold"><?= !empty($price_item['brand_long'])?$price_item['brand_long']: 'Информация отсутсвует' ;?></span></p>
						<p>Страна производителя: <br><span  class="bold"><?= !empty($price_item['country'])?$price_item['country']: '<br>' ;?></span></p>
						<p>Срок доставки: <span  class="bold">
								<?php
								if($price_item['delivery'] >= 5)
								{

									echo "<span class='tooltips' tabindex='0'>~ <span> Приблизительный срок доставки</span></span>".Helper_Custom::getDay($price_item['delivery']);
								}
								else{
                                    echo Helper_Custom::getDay($price_item['delivery']);
								}?> </span></p></div>

						<div class="product-caption-price">
							<?php if ($guest == true) : ?>
								<span class="product-caption-price-new"><span class="tooltips js_hide" tabindex="0"><i class="fa fa-info table-shopping-remove green" style="font-size: 14px;width: 20px;height: 20px;line-height: 20px;"></i><span style="width: 300px;">Цена при создании онлайн заказа</span></span>Цена: <?= round($price_item['price_final'],0) ?> грн </span>
							<?php else: ?>
								<span class="product-caption-price-new">Цена: <?=round($price_item['price_final'],0) ; ?> грн</span>
							<?php endif ?>
						</div>

						<ul class="product-caption-feature-list js_hide">
							<li>
								<?php $price_id = $price_item['id'] ?>
								<?= (empty($price_item['amount']) ? "<span id ='number_item_$price_id'>В наличии</span><span class='tooltips' tabindex='0'>&nbsp;~ <span> Неопределенное количество</span></span>" : "Доступно:&nbsp;<span id='number_item_$price_id'>". $price_item['amount'] ."</span>&nbsp;шт") ?>
							</li>
						</ul>
					</div>
                    <?php if($readMoreButton['enable']->value == 1): ?>
                        <p style="text-align: center;">
                            <a class="btn btn-lg btn-primary" style="text-align: center;" href="<?= URL::site('katalog/article/'); ?>/<?= $price_item['part_id'] ?>"><?= $readMoreButton['text']->value ?></a>
                        </p>
                    <?php endif; ?>
					<p style="text-align: center;">
						<input id="qty_<?=$price_id?>" class="product-page-qty product-page-qty-input" type="hidden" value="1" />
						<a class="btn btn-lg btn-primary add-to-cart" data-href="<?= URL::site("cart/add"); ?>?price_id=<?= $price_id ?>" data-priceitem="<?= $price_id ?>" style="text-align: center;"><i class="fa fa-shopping-cart"></i><?= $buyTextButton->value ?></a>
					</p>
				</div>
			</div>
		<?php  endforeach;  ?>

	</div>
	<div class="clearfix"></div>
<?php endif; ?>
<div class="gap"></div>
<?php echo View::factory('common/categories_block')->render(); ?>
<script type="text/javascript">
	function myfunktion(){
		$.notify({
				// options
				icon: 'fa fa-shopping-cart',
				message: 'Товар добавлен в корзину. <br>Нажмите сюда, что бы перейти в корзину.',
				url: '/cart/show',
				target: '_blank'
			},{
				// settings
				element: 'body',
				position: null,
				type: "success",
				allow_dismiss: false,
				newest_on_top: false,
				showProgressbar: false,
				placement: {
					from: "bottom",
					align: "right"
				},
				offset: 20,
				spacing: 10,
				z_index: 1031,
				delay: 5000,
				timer: 1000,
				url_target: '_blank',
				mouse_over: null,
				animate: {
					enter: 'animated fadeInDown',
					exit: 'animated fadeOutUp'
				},
				onShow: null,
				onShown: null,
				onClose: null,
				onClosed: null,
				icon_type: 'class',
				template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0}" role="alert">' +
				'<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
				'<span data-notify="icon"></span> ' +
				'<span data-notify="title">{1}</span> ' +
				'<span data-notify="message">{2}</span>' +
				'<div class="progress" data-notify="progressbar">' +
				'<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
				'</div>' +
				'<a href="{3}" target="{4}" data-notify="url"></a>' +
				'</div>'
			}
		);}
</script>
