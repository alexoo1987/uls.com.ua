<script type="text/javascript">
    var image_url = "<?=URL::site('katalog/get_images');?>";
</script>

<?php

$minValue = 10000000000;
if(count($best_parts) > 0)
{
    foreach ($best_parts as $partNew)
    {
        if(round($partNew['price'], 0) < $minValue)
        {
            $scriptPart = $partNew;
            $minValue = round($partNew['price'], 0);
        }
    }
}
?>
<?php if(isset($scriptPart)): ?>
<script>
    gtag('event', 'page_view', {
        'send_to': 'AW-941417309',
        'ecomm_pagetype': 'product',
        'ecomm_prodid': <?= $scriptPart['id']?>,
        'ecomm_totalvalue': <?= round(doubleval($scriptPart['price']) , 0)?>
    });
</script>

<?php endif; ?>
<?php echo View::factory('common/car_select')->render(); ?>
<div class="row" itemscope="" itemtype="http://schema.org/Product">

    <h1 class="title_article" itemprop="name"><?=$content_catalog->h1?></h1>

    <ol class="breadcrumb page-breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#">
        <li typeof="v:Breadcrumb"><a href="<?= URL::base()?>" rel="v:url" property="v:title">Интернет магазин автозапчастей</a>
        </li>
        <li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog')?>" rel="v:url" property="v:title">Каталог</a>
        </li>
        <?php if (count($breadcumbs) > 0) {?>
            <li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog') . '/' .  $modification_url . $breadcumbs['parent_slug']?>" rel="v:url" property="v:title"><?= $breadcumbs['parent_name'] ?></a></li>
            <li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog') . '/' .  $modification_url . $breadcumbs['cat_slug']?>" rel="v:url" property="v:title"><?= $breadcumbs['cat_name'] ?></a></li>
        <?php } ?>


        <li class="active" typeof="v:Breadcrumb"><a href="<?= URL::site('katalog/produkt').'/'.$url = Htmlparser::transliterate($part['brand'] . "-" . $part['article'] . "-" . substr($part['name'], 0, 50)) . "-" . $part['id'] ?>" rel="v:url" property="v:title"><?= $part['brand_long']." ".$part['article_long']?></a></li>
    </ol>

    <div class="col-md-12">
        <div class="row">
            <div class="col-md-4">
                <?php if(in_array( Article::get_short_article($part['article']), $top_orderitems)):?>
                    <img class="article_line" src="/images/1.png">
                    <img src="" alt="">
                <?php endif;?>
                <?php if(!empty($part['images'])):?>


                    <img class="product-img-primary img_background" title="<?= $breadcumbs['parent_name'] ?> <?= $breadcumbs['cat_name'] ?> <?= $part['brand_long']." ".$part['article_long']?> купить по доступным ценам от компании ULC" Alt="<?= $part['brand_long']." ".$part['article_long']?> купить в Украине по выгодным ценам от компании ULC" style="width:100%; position: relative;z-index: 0" src="<?= URL::base(); ?>image/tecdoc_images<?= $part['images']; ?>"/>
<!--                    <img class="product-img-primary" title="--><?//= $breadcumbs['parent_name'] ?><!-- --><?//= $breadcumbs['cat_name'] ?><!-- --><?//= $part['brand_long']." ".$part['article_long']?><!-- купить по доступным ценам от компании ULC" Alt="--><?//= $part['brand_long']." ".$part['article_long']?><!-- купить в Украине по выгодным ценам от компании ULC" style="width: 100%;position: relative;z-index: 0" src="--><?//= URL::base(); ?><!--media/img/dist/icons/as.jpg"/>-->
                    <img class="" title="<?= $breadcumbs['parent_name'] ?> <?= $breadcumbs['cat_name'] ?> <?= $part['brand_long']." ".$part['article_long']?> купить по доступным ценам от компании ULC" Alt="<?= $part['brand_long']." ".$part['article_long']?> купить в Украине по выгодным ценам от компании ULC" style="width: 50%;position: relative;opacity: 0.6;z-index: 1;top: -218px;left: 95px;" src="<?= URL::base(); ?>media/img/dist/icons/logo-for-foto.png"/>
                    <?php else:?>
                    <img src="<?= URL::base(); ?>media/img/no-image.png" style="width: 100%" title="<?= $part['brand_long']." ".$part['article_long']." ".substr($part['name'], 0, 50) . (strlen($part['name']) > 50 ? "..." : "") ?>" Alt="<?= $part['brand_long']." ".$part['article_long']." ".substr($part['name'], 0, 50) . (strlen($part['name']) > 50 ? "..." : "") ?>" itemprop="image"/>
                <?php endif;?>
            </div>
            <div class="col-md-8" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
                <link itemprop="availability" href="http://schema.org/InStock">
                <div class="_box-highlight">
                    <?php if(empty($best_parts)) : ?>
                        <span class="select_position">Предложения по этой позиции временно отсутсвуют</span>
                    <?php else: ?>
                        <span class="select_position">Выберите позицию которая вам больше подходит по срокам и стоимости</span>
                        <?php foreach ($best_parts AS $one){
                            ?>

                            <div class="col-md-5">
                                <div class="box">
                                    <table class="table product">
                                        <thead>
                                        <tr>
                                            <th><span class="bold">Срок доставки:
                                                        <?php
                                                        if($one['delivery'] >= 5)
                                                        {  echo "<span class='tooltips' tabindex='0'>~ <span> Приблизительный срок доставки</span></span>".Helper_Custom::getDay($one['delivery']); }
                                                        else{
                                                            echo Helper_Custom::getDay($one['delivery']);
                                                        }?></span></th>
                                        </tr>
                                        <tr>
                                            <th>
                                                <p>Производитель: <span class="bold"><?php if(isset($part['country'])) echo $part['country']; ?></span></p>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr style="text-align: center;">
                                            <td>
                                                <?php if ($guest == true) : ?>
                                                    <p class="product-page-price" style="text-align: center"><span class="tooltips js_hide" style="text-align: right; vertical-align: middle;" tabindex="0"><i class="fa fa-info table-shopping-remove green" style="font-size: 10px;width: 20px;height: 20px;line-height: 20px;"></i><span style="text-align: center; font-size: 16px; width: 300px;">Цена при создании онлайн заказа</span></span><?= $price = round($one['price'], 0); ?> грн</p>
                                                <?php else: ?>
                                                    <p class="product-page-price" style="text-align: center"><?= $price = Article::get_price_for_client_by_namber(round($one['price_start']), 1); ?> грн </p>
                                                <?php endif; ?>
                                                <meta itemprop="price" content="<?=$price?>">
                                                <meta itemprop="priceCurrency" content="UAH">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div style="text-align: center; margin-left: 20%;">
                                                    <ul class="product-page-actions-list">
                                                        <li class="product-page-qty-item">
                                                            <button class="product-page-qty product-page-qty-minus">-</button>
                                                            <input id="qty_<?=$one['id']?>" class="product-page-qty product-page-qty-input" disabled type="text" value="1" />
                                                            <button class="product-page-qty tooltips product-page-qty-plus">+<span> Выбрано максимальное количество</span></button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>
                                                <div style="text-align: center;">
                                                    <ul class="product-page-actions-list" style="text-align: center;">
                                                        <a class="btn btn-lg btn-primary price-btn add-to-cart"
                                                           data-href="<?= URL::site("cart/add"); ?>?price_id=<?=$one['id'] ?>" data-priceitem="<?= $one['id'] ?>"><i class="fa fa-shopping-cart"></i><?= $buyTextButton->value ?></a>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th><p class="text-muted text-sm">
                                                    <?php $id = $one['id']; ?>
                                                    <?= (empty($one['amount']) ? "<span id ='number_item_$id' class='price_item'>В наличии</span><span class='tooltips js_hide' tabindex='0'>&nbsp;~ <span> Неопределенное количество</span></span>" : "Доступно на складе:&nbsp;<span id ='number_item_$id' class='price_item'>". $one['amount'] ."</span>&nbsp;шт") ?>
                                                </p>
                                                <?php
                                                    if($one['delivery'] >= 3): ?><p style="color: red; font-size: 10px">Заказ товара при полной оплате</p>
                                                <?php endif; ?>

                                            </th>

                                        </tr>

                                        <tr>
                                            <td>
                                                <form action="<?php echo URL::site('orders/one_click') ?>" method="post" style="position: relative; padding-left: 15px; padding-right: 15px;">
                                                    <div class="form-group">
                                                        <input class="newsletter-input form-control phone" name="phone_number" placeholder="+38" <?php if(ORM::factory('Client')->logged_in()) :?> value="<?=ORM::factory('Client')->get_client()->phone; ?>" <?php endif; ?>  type="text" required/>
                                                    </div>
                                                    <input name="id_position" type="hidden" value="<?= $one['id'] ?>">
                                                    <input name="count" type="hidden" value="2">
                                                    <p>Заказать в 1 клик!</p>
                                                    <button class="btn btn-primary recall_phone" style="position: absolute; right: 15px; top: 1px;" type="submit"> <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span></button>
                                                </form>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>


                                </div>
                            </div>
                        <?php } ?>
                    <?php endif; ?>
                    <div class="gap gap-small"></div>

                </div>
            </div>
        </div>
    </div>
    <div class="gap gap-small"></div>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-3">
                <?php if(!empty($part['rating'])):?>
                    <div class="well product-rating" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
                        <p><input id="rating"  class="kv-ltr-theme-fa-star rating-loading" type="number" value="<?=$part['rating']?>"  <?=in_array(Request::$client_ip, explode(',', $part['ip'])) ? 'readonly="readonly"' : ''?>  data-min="0" data-max="5" data-step="1" data-size="xs" data-show-clear="false" data-show-caption="false" data-id="<?=$part['id']?>" data-factory="Part"/></p>
                        <p>(<span class="dist votes" itemprop="ratingCount"><?=$part['votes']?></span> голосов, оценка <span class="dist rating" itemprop="ratingValue"><?=round($part['rating'], 2)?></span> из <span itemprop="bestRating">5</span>)</p>
                    </div>
                <?php endif;?>
            </div>
            <div class="col-md-9">
                <div class="tabbable product-tabs">
                    <ul class="nav nav-tabs" id="tabs">
                        <?php if (!empty($criterias)) { ?>
                            <li class="active"><a href="#tabs-2" data-toggle="tab">Характеристики</a></li><?php } ?>
                        <?php if (!empty($applied_to)) { ?>
                            <li><a href="#tabs-3" data-toggle="tab">Применяемость</a></li><?php } ?>
                    </ul>
                    <div class="tab-content">
                        <?php if (!empty($criterias)) { ?>
                            <div class="tab-pane active" id="tabs-2">
                                <table class="table ">
                                    <tbody>
                                    <?php foreach ($criterias as $criteria): ?>
                                        <?php if($criteria['CRITERIA_DES_TEXT'] == 'Гарантия') continue;?>
                                        <tr>
                                            <td><?= $criteria['CRITERIA_DES_TEXT'] ?></td>
                                            <td><?= $criteria['CRITERIA_VALUE_TEXT'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td>Подкатегория</td>
                                        <td>
                                            <a href="<?= URL::site('katalog') . '/' .  $modification_url . $breadcumbs['parent_slug']?>"><?= $breadcumbs['parent_name'] ?></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Запчасть</td>
                                        <td>
                                            <a href="<?= URL::site('katalog') . '/' .  $modification_url . $breadcumbs['cat_slug']?>"><?= $breadcumbs['cat_name'] ?></a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div class="clearfix"></div>
                            </div>
                        <?php } ?>

                        <?php if (!empty($applied_to)) { ?>
                            <div class="tab-pane fade" id="tabs-3">
                                <div style="width: 50%; float: left;">
                                    <?php $count = 1; ?>
                                    <?php foreach ($applied_to as $applied_to_item): ?>
                                        <?= $applied_to_item['MFA_BRAND'] ?> <?= $applied_to_item['MOD_CDS_TEXT'] ?> <?= $applied_to_item['TYP_CDS_TEXT'] ?>
                                    <br>
                                    <?php if ($count > (count($applied_to) / 2)) { ?>
                                </div>
                                <div style="width: 50%; float: left;">
                                    <?php } ?>
                                    <?php $count++; ?>
                                    <?php endforeach; ?>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="gap"></div>
    <div class="seo_text"><!-- seo_text --><!-- /seo_text --></div>

</div>

<div class="gap"></div>
<span class="widget-title">Аналоги и заменители</span>

<?php if (!empty($crosses)) { ?>
    <div class="row" data-gutter="15">
        <?php foreach ($crosses as $part): ?>
            <div class="col-md-3">
                <a class="product-link"
                   href="<?= URL::site('katalog/produkt/' . Htmlparser::transliterate($part['brand'] . "-" . $part['article'] . "-" . substr($part['name'], 0, 50)) . "-" . $part['part_id']); ?>">
                    <div class="product product-sm-left ">

                        <div class="product-img-wrap">
                            <?php if (!empty($part['images'])) :?>
                                <img class="product-img"
                                     src="<?= URL::base(); ?>image/tecdoc_images/<?= $part['images']; ?>"
                                     alt=""/>
                            <?php else: ?>
                                <img class="product-img" src="<?= URL::base(); ?>media/img/no-image.png"
                                     alt=""/>
                            <?php endif; ?>
                        </div>

                        <div class="product-caption">
                            <p class="product-caption-title"><?= $part['brand_long'] ?> <?= $part['article_long'] ?> <?= Article::shorten_string($part['name'], 3) ?></p>
                            <div class="product-caption-price"><span
                                    class="product-caption-price-new"><?=  round($part['price_final'],0) ?>
                                    грн</span>
                            </div>
                            <ul class="product-caption-feature-list">
                                <li><?= ($part['amount'] == '1' || $part['amount'] == '2' || empty($part['amount']) ? "Заканчивается" : "Есть в наличии") ?></li>
                            </ul>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php } ?>

<div class="gap"></div>
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
                '<a href="{3}" data-notify="url"></a>' +
                '</div>'
            }
        );}
</script>
