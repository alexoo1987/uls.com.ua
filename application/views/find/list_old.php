<script type="text/javascript">
    var image_url = "<?=URL::site('katalog/get_images');?>";
</script>
<?php $current_url = URL::query(); ?>
<div class="gap"></div>
<a type="button" style="margin: 0px AUTO;margin-bottom: 20px;" href="/findexpert/index<?= $current_url?>" class="btn btn-primary vin_code_long">Режим эксперта&nbsp;&nbsp;<span class="tooltips" tabindex="0"><i class="fa fa-info table-shopping-remove" style="font-size: 14px;width: 20px;height: 20px;line-height: 20px;"></i><span style="width: 300px;"> Более широкий <br> выбор</span></span></a>
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
            <!--			--><?php //
//				if($part->priceitems->count_all() == 0) continue;
//			?>
            <tr onclick="window.location = '<?=URL::site('find/index');?>?art=<?=$part->article?>&brand=<?=$part->brand?>'">
                <td><?=$part->get_brand()?></td>
                <td><?=$part->article_long?></td>
                <td><?=Article::shorten_string($part->name, 3)?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php } elseif(!$found) { ?>
    <p>По вашему запросу "<?=$_GET['art']?>" ничего не найдено</p>
<?php } ?>

<?php if(count($parts) <= 1): ?>
    <?php foreach($groups as $group): ?>

        <?php if(count($group[1]) > 0): ?>
            <ul class="nav nav-tabs">
                <h1 class="active"><?=($group[0] ? "<span>".$group[0]."</span>" : "")?></h1>
            </ul>
            <div class="row" data-gutter="15">
                <?php foreach($group[1] as $key => $price_item) :
//						if (is_null($price_item->part->id)) continue;?>

                    <div class="col-md-3">
                        <?php if(in_array( Article::get_short_article($price_item->part->article_long), $top_orderitems)):?>
                            <img class="find_line" src="/images/1.png">
                        <?php endif;?>
                        <div class="product">
                            <div class="product-img-wrap">

                                <?php if(!empty($price_item->part->id)) {?>
                                <a class="product-link" href="<?= URL::site('katalog/article/'); ?>/<?= $price_item->part->id ?>">
                                    <?php }else{?>
                                    <a class="product-link" href="<?= URL::site('katalog/article/'); ?>/<?= $price_item->id ?>">
                                        <?php }?>
                                        <?php if (!empty($price_item->part->images)) :?>
                                            <img class="product-img-primary" src="<?= URL::base(); ?>image/tecdoc_images/<?= $price_item->part->images; ?>"
                                                 alt="" />

                                            <img class="product-img-alt" src="<?= URL::base(); ?>image/tecdoc_images/<?= $price_item->part->images; ?> "
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
                                <h5 class="product-caption-title"><?=$price_item->part->get_brand()?> <?=$price_item->part->article_long?> <?=Article::shorten_string($price_item->part->name, 3)?></h5>
                                <p>Производитель: <br><span  class="bold"><?=$price_item->part->get_brand()?></span></p>
                                <p>Срок доставки: <span  class="bold">
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
                                        }?> </span></p>

                                <div class="product-caption-price">
                                    <?php if ($guest == true) : ?>
                                        <span class="product-caption-price-new"><span class="tooltips" tabindex="0"><i class="fa fa-info table-shopping-remove green" style="font-size: 14px;width: 20px;height: 20px;line-height: 20px;"></i><span style="width: 300px;"> Сделайте онлайн заказ - получите скидку</span></span> <?= $price_item->get_price_for_client() ?> грн </span>
                                        <br>
                                        <span class="product-page-price-unlogin" style="text-align: center; padding-top: 10px;"><?=$price_item->get_price_for_client(false, false, 1); ?> грн </span>
                                    <?php else: ?>
                                        <span class="product-caption-price-new"><?=$price_item->get_price_for_client(); ?> грн</span>
                                    <?php endif ?>
                                </div>

                                <ul class="product-caption-feature-list">
                                    <li>
                                        <?= (empty($price_item->amount) ? "<span id ='number_item_$price_item->id'>В наличии</span><span class='tooltips' tabindex='0'>&nbsp;~ <span> Неопределенное количество</span></span>" : "Доступно:&nbsp;<span id='number_item_$price_item->id'>". $price_item->amount ."</span>&nbsp;шт") ?>
                                    </li>
                                </ul>
                            </div>
                            <p style="text-align: center;">
                                <input id="qty_<?=$price_item->id?>" class="product-page-qty product-page-qty-input" type="hidden" value="1" />
                                <a class="btn btn-lg btn-primary add-to-cart" data-href="<?= URL::site("cart/add"); ?>?price_id=<?= $price_item->id ?>" data-priceitem="<?= $price_item->id ?>" style="text-align: center;"><i class="fa fa-shopping-cart"></i>Купить</a>
                            </p>
                        </div>
                    </div>
                <?php  endforeach;  ?>

            </div>
            <div class="clearfix"></div>
        <?php endif; ?>
    <?php endforeach; ?>
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
