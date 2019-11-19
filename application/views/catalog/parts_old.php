<script type="text/javascript">
	var image_url = "<?=URL::site('katalog/get_images');?>";
</script>
<?php echo View::factory('common/car_select')->render(); ?>
<div class="container">
	<h1><?=$h1?></h1>
	<ol class="breadcrumb page-breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#">
		<li typeof="v:Breadcrumb"><a href="<?= URL::base()?>" rel="v:url" property="v:title">Интернет магазин автозапчастей</a>
		</li>
		<li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog')?>" rel="v:url" property="v:title">Каталог</a>
		</li>
		<?php if ($category->get_parent()) {?>
		<li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog') . '/' .  $modification_url . $category->get_parent()->slug?>" rel="v:url" property="v:title"><?= $category->get_parent()->name ?></a></li>
		<?php } ?>
		<li class="active" typeof="v:Breadcrumb"><span rel="v:url" property="v:title"><?= $category->name ?></span></li>
	</ol>
	<div class="gap-small"></div>
	<?php
			if (empty($parts)) { ?>
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
									<div class="category-filters-section">
										<nav class="navbar-default navbar-main-white yamm" style="margin-left: 25%; z-index: auto;">
											<div class="collapse navbar-collapse navbar-collapse-no-pad" id="main-nav-collapse">
												<ul class="nav navbar-nav" style="padding-left: none;">
													<li class="dropdown"><a href="<?= URL::site("katalog"); ?>" style="text-alight: center;"><span class="block widget-title-sm">Категории&nbsp;<i class="fa fa-chevron-down"></i></span></a>
														<ul class="dropdown-menu dropdown-menu-category" style="left:-33.3333333%;;">
															<?php echo View::factory('common/categories_menu')->render(); ?>
														</ul>
													</li>
												</ul>
											</div>
										</nav>
									</div>
									<?php if (!empty($filter)){?>
									<form method="GET">
									<?php if (!empty($filter['location'])) { ?>
										<div class="category-filters-section">
											<span class="block widget-title-sm">Сторона установки</span>
											<?php foreach ($filter['location'] AS $key => $location) { ?>
											<div class="checkbox">
												<label>
													<input class="i-check" type="checkbox" name="location[]" <?= (isset($_GET['location']) AND in_array($location, $_GET['location'])) ? 'checked' : '' ?> value="<?= $location ?>" /><?= $location ?>
												</label>
											</div>
											<?php } ?>
										</div>
									<?php } ?>
									<?php if (!empty($filter['brand'])) { ?>
										<div class="category-filters-section">
											<span class="block widget-title-sm">Бренд</span>
											<?php foreach ($filter['brand'] AS $key => $brand) { ?>
												<div class="checkbox">
													<label>
														<input class="i-check" type="checkbox" name="brand[]" <?= (isset($_GET['brand']) AND in_array($brand, $_GET['brand'])) ? 'checked' : '' ?> value="<?= $brand ?>" /><?= $brand ?>
													</label>
												</div>
											<?php } ?>
											<?php } ?>
										</div>
								</form>
							</aside>
							<?php } ?>
						</div>
					<div class="col-md-9">
						<div class="row" data-gutter="15">
							<?php foreach ($parts as $part) : ?>
								<div class="col-md-4">
									<?php if(in_array( Article::get_short_article($part['article_nr']), $top_orderitems)):?>
                                    <img class="find_line" src="/images/1.png">
									<?php endif;?>
									<div
										class="product<?php if (!array_key_exists($part['id'], $prices)): ?> not-available<?php endif; ?>">
										<div class="product-img-wrap">

<!--											--><?//= $part['images']; ?>
											<a class="product-link"
											   href="<?= URL::site('katalog/produkt/'); ?>/<?= $url = Htmlparser::transliterate($part['brand'] . "-" . $part['article_nr'] . "-" . substr($part['description'], 0, 50)) . "-" . $part['part_id'] ?>">
												<?php if (!empty($part['part_img'])) :
//													$tecdoc = ORM::factory('Tecdoc');
//													$graphics = $tecdoc->get_graphics($part['id']);
													?>
													<img class="product-img-primary" src="<?= URL::base(); ?>image/tecdoc_images/<?= $part['part_img']; ?>"
														 alt="" />

													<img class="product-img-alt" src="<?= URL::base(); ?>image/tecdoc_images/<?= $part['part_img']; ?> "
														 alt=""/>

												<?php else: ?>
													<img class="product-img-primary" src="<?= URL::base(); ?>media/img/no-image.png"
														 alt="" itemprop="image"/>
													<img class="product-img-alt" src="<?= URL::base(); ?>media/img/no-image.png"
														 alt="" itemprop="image"/>
												<?php endif ?>
											</a>
										</div>

										<?php $price_item = null; ?>
										<?php if (array_key_exists($part['id'], $prices)): ?>
											<?php
											$price_item = $prices[$part['id']]['price_item'];
											?>

										<?php else: ?>
											<span class="int">&nbsp;</span>
											<span class="current">&nbsp;</span>
											<p class="available">
												<span class="availability not-available">Нет в наличии</span>
											</p>
										<?php endif; ?>
										<div class="product-caption">
											<a href="<?= URL::site('katalog/produkt/'); ?>/<?= $url ?>"><span class="block product-caption-title "><?= $part['brand'] ?> <?= $part['article_nr'] ?> <?= substr($part['description'], 0, 50) ?></span></a>
											<p>Производитель: <br><span class="bold"><?= $part['brand'] ?></span></p>
											<p>Срок доставки: <span class="bold">
													<?php
													if($price_item->delivery >= 5)
													{
														echo "<span class='tooltips' tabindex='0'>~ <span> Приблизительный срок доставки</span></span>".$price_item->delivery." дн.";
													}
													else{
														if($price_item->delivery > 0)
														{
															echo $price_item->delivery." дн.";
														}
														else
														{
															echo "1 дн.";
														}
													}?></span></p>
											<div class="product-caption-price">
												<?php if ($guest == true) : ?>
												<span class="product-caption-price-new"><span class="tooltips" tabindex="0"><i class="fa fa-info table-shopping-remove green" style="font-size: 14px;width: 20px;height: 20px;line-height: 20px;"></i><span style="width: 300px;"> Сделайте онлайн заказ - получите скидку</span></span> <?= $price_item->get_price_for_client() ?> грн </span>
												<br>
													<span class="product-page-price-unlogin" style="text-align: center; padding-top: 10px;"><?= $price_item->get_price_for_client(false, false, 1) ?> грн </span>
												<?php else: ?>
													<span class="product-caption-price-new"><?= $price_item->get_price_for_client() ?> грн</span>
												<?php endif; ?>
											</div>
											<ul class="product-caption-feature-list">
												<li>
													<?= (empty($price_item->amount) ? "<span id ='number_item_$price_item->id'>В наличии</span><span class='tooltips' tabindex='0'>&nbsp;~ <span> Неопределенное количество</span></span>" : "Доступно:&nbsp;<span id='number_item_$price_item->id'>". $price_item->amount ."</span>&nbsp;шт") ?>
												</li>
											</ul>
										</div>
										<p style="text-align: center;">
											<input id="qty_<?=$price_item->id?>" class="product-page-qty product-page-qty-input" type="hidden" value="1" />
											<a class="btn btn-lg btn-primary add-to-cart" data-href="<?= URL::site("cart/add"); ?>?price_id=<?= $price_item->id ?>" data-priceitem="<?= $price_item->id ?>" " style="text-align: center;"><i class="fa fa-shopping-cart"></i>Купить</a>
										</p>

									</div>
								</div>
							<?php endforeach; ?>
						</div>
						<div class="row">
							<div class="col-md-6_1">
							</div>
							<div class="col-md-6_1">
								<nav>
									<?=$pagination?>
								</nav>
							</div>
						</div>
					</div>
				</div>
		<?php } ?>

	<?php if (!isset($_GET['page'])) { ?>
		<div class="clearfix seo-text col-md-12"><?=$content_text?></div>
	<?php } ?>

	<?php echo View::factory('common/seo_block')->render(); ?>

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
