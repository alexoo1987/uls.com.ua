<script type="text/javascript">
	var image_url = "<?=URL::site('katalog/get_images');?>";
</script>
<?php echo View::factory('common/car_select')->render(); ?>

<div class="container">
	<h1 class="widget-title-lg"><?=$content_catalog->h1?></h1>
	<ol class="breadcrumb page-breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#">
		<li typeof="v:Breadcrumb"><a href="<?= URL::base()?>" rel="v:url" property="v:title">Интернет магазин автозапчастей</a>
		</li>
		<li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog')?>" rel="v:url" property="v:title">Каталог</a>
		</li>
		<?php if(isset($car['manuf_url']) AND $car['manuf_url']): ?>
			<li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog').'/'.$car['manuf_url']?>" rel="v:url" property="v:title"><?=$car['manuf_name'] ?></a>
			</li>
		<?php endif; ?>
		<?php if(isset($car['model_url']) AND $car['model_url']): ?>
			<li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog').'/'.$car['manuf_url'].'/'.$car['model_url']?>" rel="v:url" property="v:title"><?=$car['model_name'] ?></a>
			</li>
		<?php endif; ?>
		<?php if(isset($car['type_url']) AND $car['type_url']): ?>
			<li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog').'/'.$car['manuf_url'].'/'.$car['model_url'].'/'.$car['type_url']?>" rel="v:url" property="v:title"><?=$car['type_name'] ?></a>
			</li>
		<?php endif; ?>
		<?php if ($category->get_parent()) {?>
			<li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog') . '/' .  $modification_url . $category->get_parent()->slug?>" rel="v:url" property="v:title"><?= $category->get_parent()->name ?></a></li>
		<?php } ?>
		<?php if($brand_ids):?>
			<li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog') . '/' .  $modification_url . $category->get_parent()->slug?>" rel="v:url" property="v:title"><?= $category->name ?></a></li>
<!--			<li class="active" typeof="v:Breadcrumb"><span rel="v:url" property="v:title">--><?//= $brand_ids ?><!--</span></li>-->
			<?php $filter_url = 'filter-'.str_replace(',', '-', $brand_ids); ?>
			<li class="active" typeof="v:Breadcrumb"><a href="<?= URL::site('katalog').'/'.$modification_url.$category->slug.'/'.$filter_url ?>" rel="v:url" property="v:title"><?= $brand_ids ?></a></li>
		<?php else: ?>
			<li class="active" typeof="v:Breadcrumb"><a href="<?= URL::site('katalog').'/'.$modification_url.$category->slug ?>" rel="v:url" property="v:title"><?= $category->name?></a></li>
		<?php endif; ?>

	</ol>
	<div class="gap-small"></div>
	<?php
			if (empty($priceitems)) { ?>
				<p>Ненайдена запчасть? Позвоните нам и мы сможем вам подобрать нужную запчасть.</p>
				<div class="col-md-3">
					<div class="row">
						<p>(044) 361-96-64</p>
						<p>(067) 291-18-25</p>
						<p>(095) 053-00-35</p>
						<p>(063) 631-84-39</p>
						<br>
					</div>
				</div>
				<div class="col-md-4">
<!--					<h4 class="widget-title-sm">Обратная связь</h4>-->
					<form action="<?php echo URL::site('authorization/recall') ?>" method="post">
						<div class="form-group">
							<label style="font-weight: 800">Оставьте свой номер телефона, и мы обьязательно с Вами свяжемся</label>
							<input class="newsletter-input form-control phone" name="phone_number" placeholder="Телефон" type="text" required/>
						</div>
						<input class="btn btn-primary" type="submit" value="Свяжитесь со мной!"/>
					</form>
				</div>
				<br>
			<?php } else { ?>
				<div class="row">
					<div class="col-md-3">
						<aside class="category-filters">
							
							<div class="category-filters-section" id="brands_filters">
								<span class="block widget-title-sm">Бренд</span>
								<img src="/media/img/Loader.gif" style="width: 100px">
							</div>
						</aside>
					</div>
					<div class="col-md-9">
						<div class="row parts_blocks" data-gutter="15">
							<div id="first_parts_block">
							<?php foreach ($priceitems as $part) : ?>
								<?php if($part['price_start']<=1) {continue;} ?>
								<div class="col-md-4">
									<?php if(in_array( Article::get_short_article($part['article']), $top_orderitems)):?>
										<img class="find_line" src="/images/1.png">
									<?php endif;?>
									<div
										class="product">
										<div class="product-img-wrap">

											<a class="product-link"
											   href="<?= $url = Helper_Url::getPartUrl($part, [], true) ?>">
												<?php if (!empty($part['images'])) :
													?>
													<img class="product-img-primary" title=" <?= $part['brand_long']." ".$part['article_long']?> купить по доступным ценам от компании ULC" Alt="<?= $part['brand_long']." ".$part['article_long']?> купить в Украине по выгодным ценам от компании ULC" src="<?= URL::base(); ?>image/tecdoc_images<?= $part['images']; ?>" />

													<img class="product-img-alt" title=" <?= $part['brand_long']." ".$part['article_long']?> купить по доступным ценам от компании ULC" Alt="<?= $part['brand_long']." ".$part['article_long']?> купить в Украине по выгодным ценам от компании ULC" src="<?= URL::base(); ?>image/tecdoc_images<?= $part['images']; ?> " />

												<?php else: ?>
													<img class="product-img-primary" src="<?= URL::base(); ?>media/img/no-image.png" title=" <?= $part['brand_long']." ".$part['article_long']?> купить по доступным ценам от компании ULC" Alt="<?= $part['brand_long']." ".$part['article_long']?> купить в Украине по выгодным ценам от компании ULC" itemprop="image"/>
													<img class="product-img-alt" title="<?= $part['brand_long']." ".$part['article_long']?> купить по доступным ценам от компании ULC" Alt="<?= $part['brand_long']." ".$part['article_long']?> купить в Украине по выгодным ценам от компании ULC" src="<?= URL::base(); ?>media/img/no-image.png" itemprop="image"/>
												<?php endif ?>
											</a>
										</div>

										<div class="product-caption">
											<a href="<?= $url ?>"><span class="block product-caption-title "><?= $part['brand_long'] ?> <?= $part['article_long'] ?> <?= substr($part['name'], 0, 50) ?></span></a>
											<div class="js_hide"><p>Производитель: <br><span class="bold"><?= $part['brand'] ?></span></p>
											<p>Страна производителя: <br><span class="bold"><?= !empty($part['country'])?$part['country']: '<br>' ;?></span></p>
											<p>Срок доставки: <span class="bold">
													<?php
													if($part['delivery'] >= 5)
													{
														echo "<span class='tooltips' tabindex='0'>~ <span> Приблизительный срок доставки</span></span>".Helper_Custom::getDay($part['delivery']);
													}
													else{
                                                        echo Helper_Custom::getDay($part['delivery']);
													}?></span></p></div>
											<div class="product-caption-price">
												<?php if ($guest == true) : ?>
													<span class="product-caption-price-new"><span class="tooltips js_hide" tabindex="0"><i class="fa fa-info table-shopping-remove green" style="font-size: 14px;width: 20px;height: 20px;line-height: 20px;"></i><span style="width: 300px;">Цена при создании онлайн заказа</span></span>Цена: <?= round($part['price_final'],0) ?> грн </span>
												<?php else: ?>
													<span class="product-caption-price-new">Цена: <?=  Article::get_price_for_client_by_namber(round($part['price_start']), 1); ?> грн</span>
												<?php endif; ?>
											</div>
											<ul class="product-caption-feature-list js_hide">
												<li>
													<?php $id = $part['id']; ?>
													<?= (empty($part['amount'] ) ? "<span id ='number_item_$id'>В наличии</span><span class='tooltips' tabindex='0'>&nbsp;~ <span> Неопределенное количество</span></span>" : "Доступно:&nbsp;<span id='number_item_$id'>". $part['amount'] ."</span>&nbsp;шт") ?>
												</li>
											</ul>
										</div>
                                        <?php if($readMoreButton['enable']->value == 1): ?>
                                            <p style="text-align: center;">
                                                <a class="btn btn-lg btn-primary" style="text-align: center;" href="<?= $url ?>"><?= $readMoreButton['text']->value ?></a>
                                            </p>
                                        <?php endif; ?>
										<p style="text-align: center;">
											<input id="qty_<?=$part['id']?>" class="product-page-qty product-page-qty-input" type="hidden" value="1" />
											<a class="btn btn-lg btn-primary add-to-cart" data-href="<?= URL::site("cart/add"); ?>?price_id=<?= $part['id'] ?>" data-priceitem="<?= $part['id'] ?>" " style="text-align: center;"><i class="fa fa-shopping-cart"></i><?= $buyTextButton->value ?></a>
										</p>
<!--                                        <p style="text-align: center;">-->
<!--                                            <input id="qty_--><?//=$part['id']?><!--" class="product-page-qty product-page-qty-input" type="hidden" value="1" />-->
<!--                                            <a class="btn btn-lg btn-primary buy-one-click" data-href="--><?//= URL::site("cart/add"); ?><!--?price_id=--><?//= $part['id'] ?><!--" data-priceitem="--><?//= $part['id'] ?><!--" " style="text-align: center;"><i class="fa fa-shopping-cart"></i>КУПИТЬ В ОДИН КЛИК</a>-->
<!--                                        </p>-->
                                        <form action="<?php echo URL::site('orders/one_click') ?>" method="post" style="position: relative; padding-left: 15px; padding-right: 15px;">
                                            <div class="form-group">
                                                <input class="newsletter-input form-control phone" name="phone_number" placeholder="+38" <?php if(ORM::factory('Client')->logged_in()) :?> value="<?=ORM::factory('Client')->get_client()->phone; ?>" <?php endif; ?>  type="text" required/>
                                            </div>
                                            <input name="id_position" type="hidden" value="<?= $part['id'] ?>">
                                            <input name="count" type="hidden" value="2">
                                            <p>Заказать в 1 клик!</p>
                                            <button class="btn btn-primary recall_phone" style="position: absolute; right: 15px; top: 1px;" type="submit"> <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span></button>
                                        </form>
									</div>
								</div>
								<?php endforeach; ?>
							</div>


						</div>
					</div>
				</div>
		<?php } ?>

	<?php if ($pagination['total'] != 1 AND !empty($priceitems)):?>
		<div class="pag_block">
			<ul class="pagination">
				<?php if ($pagination['current'] == 1):?>
					<li class="pagination__item pagination__item--prev pagination__item--disabled"><a href="<?=$url_link ?>" class="pagination__item-link">← Prev</a></li>
					<li class="pagination__item active"><a rel="canonical" href="<?=$url_link?>" class="pagination__item-link"><?= $pagination['current'];?></a></li>
				<?php else: ?>
					<li class="pagination__item pagination__item--prev "><a rel="prev" href="<?=$pagination['current']==2 ? $url_link : $url_link."/page-".($pagination['current'] - 1)?>" class="pagination__item-link" data-id="<?= $pagination['current'] - 1;?>">← Prev</a></li>
					<li class="pagination__item"><a href="<?=$url_link?>" class="pagination__item-link" data-id="1">1</a></li>
				<?php endif; ?>

				<?php if ($pagination['current'] > 3): ?>
					<li class="pagination__item pagination__item--separator"><a>...</a></li>
				<?php endif; ?>


				<?php for ($page=2; $page <= $pagination['total']-1; $page ++): ?>

					<?php if($pagination['current'] == $page):?>
						<li class="pagination__item active"><a rel="canonical" href="<?=$url_link."/page-".$page?>" class="pagination__item-link"><?= $page ?></a></li>
					<?php else: ?>
						<?php if ($page + 1 == $pagination['current'] OR $page - 1 == $pagination['current']):?>
							<li class="pagination__item"><a href="<?=$url_link."/page-".$page?>" class="pagination__item-link" data-id="><?= $page ?>"><?= $page ?></a></li>
						<?php endif; ?>
					<?php endif; ?>

				<?php endfor; ?>

				<?php if($pagination['current'] + 2 <= $pagination['total']): ?>
					<li class="pagination__item pagination__item--separator"><a>...</a></li>
				<?php endif; ?>

				<?php if($pagination['current'] == $pagination['total']): ?>
					<li class="pagination__item active"><a rel="canonical" href="<?=$url_link."/page-".$pagination['current']?>" class="pagination__item-link"><?= $pagination['current'];?></a></li>
					
				<?php else: ?>
					<li class="pagination__item"><a href="<?=$url_link."/page-".$pagination['total']?>" class="pagination__item-link" data-id="<?= $pagination['total'];?>"><?= $pagination['total'];?></a></li>
					<li class="pagination__item pagination__item--next"><a rel="next" href="<?=$url_link."/page-".($pagination['current'] + 1)?>" class="pagination__item-link" data-id="<?= $pagination['current'] + 1;?>">Next →</a></li>
				<?php endif; ?>
			</ul>
		</div>
	<?php endif; ?>

	<div class="col-md-12">
		<div class="row">
			<?php if(count($unother_models) > 0): ?>
				<p>В продаже имеется <?= $category->name ?> на следующие модели <?= $manufacturer ?>: </p>
				<?php
				$in_column = ceil(count($unother_models) / 3);
				$count = 0;
				for ($column = 0; $column < 3; $column++) : ?>
					<div class="col-md-4">
						<ul class="no-styled-ul new_manuf_list catalog-list">
							<?php foreach (array_slice($unother_models, $column * $in_column, $in_column) as $value):?>
								<li>
									<a href="<?= Helper_Url::createUrl('katalog/'.$manufacturer.'/'.$value['url'].'/'.$category->slug);?>">
										<span class="dist"><?= $value['short_name'] ?></span><?php /*<br>
									(<?=$start_month.".".$start_year." - ".$end_month.".".$end_year?>) */?>
									</a>
								</li>
							<?php endforeach;?>
						</ul>
					</div>
				<?php endfor; ?>
			<?php endif; ?>
		</div>
	</div>

	<div class="col-md-12">
		   <div class="col-md-4 col-md-offset-4">
				  <a style="display : none" id="more_details" class="btn btn-lg btn-primary" data-activebr="<?=$active_filter;?>" data-linkreal="<?= $url_link; ?>" data-offset="30" data-type="<?= $info['modification_slug'];?>" data-category="<?= $info['category_id'];?>" data-manuf="<?= $info['manufacturer_slug'];?>" data-model="<?= $info['model_slug'];?>" ><i class="fa fa-circle-o-notch"></i>Загрузить еще 30</a>
		   </div>
   </div>

	<?php if (!isset($_GET['page'])) { ?>
<!--		<div class="clearfix seo-text col-md-12">--><?//=$content_text?><!--</div>-->
	<?php } ?>


</div>
<div class="col-md-12" style="margin-top: 5px">
        <?php echo $content_catalog->content; ?>
</div>

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



