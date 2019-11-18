<script type="text/javascript">
    var image_url = "<?=URL::site('katalog/get_images');?>";
</script>


<?php echo View::factory('common/car_select')->render(); ?>
<div class="row" itemscope="" itemtype="http://schema.org/Product">
    <ol class="breadcrumb page-breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#">
        <li typeof="v:Breadcrumb"><a href="<?= URL::base()?>" rel="v:url" property="v:title">Интернет магазин автозапчастей</a>
        </li>
        <li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog')?>" rel="v:url" property="v:title">Каталог</a>
        </li>
        <?php if ($category AND $category->get_parent()) {?>
            <li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog') . '/' .  $modification_url . $category->get_parent()->slug?>" rel="v:url" property="v:title"><?= $category->get_parent()->name ?></a></li>
        <?php } ?>
        <li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog') . '/' .  $modification_url . ($category ? $category->slug : '')?>" rel="v:url" property="v:title"><?= ($category ? $category->name : '') ?></a>

        <li class="active" typeof="v:Breadcrumb"><span rel="v:url" property="v:title">
                <?php if (!empty($part_obj->id)): ?>
                    <?= $part_obj->get_brand() ?>
                    <?= $part_obj->article_long ?>
                    <?= Article::shorten_string($part_obj->name, 3) ?>
                <?php elseif(!empty($part_obj->brand)) : ?>
                    <?= $part_obj->brand ?>
                    <?= $part_obj->article ?>
                <?php else: ?>
                    <?= $part['brand'] ?>
                    <?= $part['article_nr'] ?>
                    <?= substr($part['description'], 0, 50) . (strlen($part['description']) > 50 ? "..." : "") ?>
                <?php endif ?></span>
        </li>
    </ol>
    <?php if (empty($criterias) && empty($graphics) && empty($applied_to) && empty($part_obj) && empty($groups)) { ?>
        <p>Информация о запчасти отсутствует</p>

    <?php } else { ?>
    <h1 class="title_article" itemprop="name">
        <?php if (!empty($part_obj->id)): ?>
            <?= $part_obj->get_brand() ?>
            <?= $part_obj->article_long ?>
            <?= Article::shorten_string($part_obj->name, 3) ?>
        <?php elseif(!empty($part_obj->brand)) : ?>
            <?= $part_obj->brand ?>
            <?= $part_obj->article ?>
        <?php else: ?>
            <?= $part['brand'] ?>
            <?= $part['article_nr'] ?>
            <?= substr($part['description'], 0, 50) . (strlen($part['description']) > 50 ? "..." : "") ?>
        <?php endif ?></h1>

    <div class="col-md-6">
            <div class="product-page-product-wrap jqzoom-stage jqzoom-stage-lg">
            <div class="clearfix">
                <?php if(in_array( Article::get_short_article($part_obj->article), $top_orderitems)):?>
                <img class="article_line" src="/images/1.png">
                <?php endif;?>
                <?php if (is_array($price_item)):?>
                    <?php if (!empty($price_item[0]->part->images)) { ?>
                        <img class="product-img-primary"
                             src="<?= URL::base(); ?>image/tecdoc_images/<?= $price_item[0]->part->images; ?>" alt="" />
                    <?php } else { ?>
                        <a href="#" id="jqzoom"
                           data-rel="gal-1">
                            <img src="<?= URL::base(); ?>media/img/no-image.png"
                                 alt="noimage" itemprop="image"/>
                        </a>
                    <?php } ?>
                <?php else:?>
                    <?php if (!empty($price_item->part->images)) { ?>
                        <img class="product-img-primary" src="<?= URL::base(); ?>image/tecdoc_images/<?= $price_item->part->images; ?>" alt="" />
                    <?php } else { ?>
                        <a href="#" id="jqzoom"
                           data-rel="gal-1">
                            <img src="<?= URL::base(); ?>media/img/no-image.png"
                                 alt="noimage" itemprop="image"/>
                        </a>
                    <?php } ?>
                <?php endif; ?>

            </div>
        </div>
    </div>
    <div class="col-md-8" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
        <link itemprop="availability" href="http://schema.org/InStock">
        <div class="_box-highlight">
            <?php if ($price_item){ ?>

            <?php if (is_array($price_item)){ ?>
                <h4><b>Выберите позицию которая вам больше подходит по срокам и стоимости</b></h4>
                <?php foreach ($price_item AS $one){ ?>
                    <div class="col-md-5">
                        <div class="box">
                            <table class="table product">
                                <thead>
                                <tr>
                                    <th><h4>Срок доставки: <b>
                                                <?php
                                                if($one->delivery >= 5)
                                                {
                                                    echo "<span class='tooltips' tabindex='0'>~ <span> Приблизительный срок доставки</span></span>".$one->delivery." дн.";
                                                }
                                                else{
                                                    if($one->delivery > 0)
                                                    {
                                                        echo $one->delivery." дн.";
                                                    }
                                                    else
                                                    {
                                                        echo "1 дн.";
                                                    }

                                                }?></b></h4></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr style="text-align: center;">
                                    <td>
                                        <?php if ($guest == true) : ?>

                                            <p class="product-page-price" style="text-align: center"><span class="tooltips" style="text-align: right; vertical-align: middle;" tabindex="0"><i class="fa fa-info table-shopping-remove green" style="font-size: 10px;width: 20px;height: 20px;line-height: 20px;"></i><span style="text-align: center; font-size: 16px; width: 300px;"> Сделайте онлайн заказ - <br>получите скидку</span></span><?= $price = $one->get_price_for_client() ?> грн</p>
                                            <p class="product-page-price" style="text-align: center; font-size: 30px; font-weight: 400"><?= $price = $one->get_price_for_client(false, false, 1) ?> грн </p>
                                        <?php else: ?>
                                            <p class="product-page-price" style="text-align: center"><?= $price = $one->get_price_for_client() ?> грн </p>
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
                                                    <input id="qty_<?=$one->id?>" class="product-page-qty product-page-qty-input" disabled type="text" value="1" />
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
                                                   data-href="<?= URL::site("cart/add"); ?>?price_id=<?= $one->id ?>" data-priceitem="<?= $one->id ?>"><i class="fa fa-shopping-cart"></i>Купить</a>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
<!--                                <tr>-->
<!--                                    <td>-->
<!--                                        <form action="--><?php //echo URL::site('orders/one_click') ?><!--" method="post" style="position: relative; padding-left: 15px; padding-right: 15px;">-->
<!--                                            <div class="form-group">-->
<!--                                                <input class="newsletter-input form-control phone" name="phone_number" placeholder="+38" --><?php //if(ORM::factory('Client')->logged_in()) :?><!-- value="--><?//=ORM::factory('Client')->get_client()->phone; ?><!--" --><?php //endif; ?><!--  type="text" required/>-->
<!--                                            </div>-->
<!--                                            <p>Заказать в 1 клик!</p>-->
<!--                                            <input class="btn btn-primary" type="submit" value="Свяжитесь со мной!"/>-->
<!--                                            <button class="btn btn-primary recall_phone" style="position: absolute; right: 15px; top: 1px;" type="submit"> <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span></button>-->
<!--                                        </form>-->
<!--                                    </td>-->
<!--                                </tr>-->
                                <tr>
                                    <th><p class="text-muted text-sm">
                                            <?= (empty($one->amount) ? "<span id ='number_item_$one->id' class='price_item'>В наличии</span><span class='tooltips' tabindex='0'>&nbsp;~ <span> Неопределенное количество</span></span>" : "Доступно на складе:&nbsp;<span id ='number_item_$one->id' class='price_item'>". $one->amount ."</span>&nbsp;шт") ?>
                                        </p></th>
                                </tr>
                                </tbody>
                            </table>


                        </div>
                    </div>
                <?php }
            } else { ?>
<!--                            sdasdda-->
                <div class="col-md-5">
                    <div class="box">
                        <table class="table product">
                            <thead>
                            <tr>
                                <th><h4>Срок доставки: <b>
                                            <?php
                                            if($price_item->delivery >= 5)
                                            {
                                                echo "<span class='tooltips' tabindex='0'>&nbsp;~ <span> Приблизительный срок доставки</span></span>".$price_item->delivery." дн.";
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

                                            }?></b></h4></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr style="text-align: center;">
                                <td>
                                    <script type="text/javascript">
                                        var google_tag_params = {
                                            dynx_itemid: '<?=$price_item->id?>',
                                            dynx_pagetype: 'offerdetail',
                                            dynx_totalvalue: <?= $price = $price_item->get_price_for_client() ?>
                                        };
                                    </script>
                                    <?php if ($guest == true) : ?>
                                    <p class="product-page-price" style="text-align: center;"><span class="tooltips" style="vertical-align: middle;" tabindex="0"><i class="fa fa-info table-shopping-remove green" style="font-size: 10px;width: 20px;height: 20px;line-height: 20px;"></i><span style="text-align: center; font-size: 16px; width: 300px;"> Сделайте онлайн заказ - <br>получите скидку</span></span> <?= $price = $price_item->get_price_for_client() ?> грн </p>
                                    <p class="product-page-price" style="text-align: center; font-size: 30px; font-weight: 400"><?= $price = $price_item->get_price_for_client(false, false, 1) ?> грн </p>
                                    <?php else: ?>
                                        <p class="product-page-price" style="text-align: center;"><?= $price = $price_item->get_price_for_client() ?> грн</p>
                                    <?php endif ?>
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
                                                <input id="qty_<?=$price_item->id?>" class="product-page-qty product-page-qty-input" disabled type="text" value="1" />
                                                <button class="product-page-qty tooltips product-page-qty-plus">+<span>Выбрано максимальное количество</span></button>
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
                                               data-href="<?= URL::site("cart/add"); ?>?price_id=<?= $price_item->id ?>" data-priceitem="<?= $price_item->id ?>"><i class="fa fa-shopping-cart"></i>Купить</a>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th><p class="text-muted text-sm">
                                        <?= (empty($price_item->amount) ? "<span id ='number_item_$price_item->id' class='price_item'>В наличии</span><span class='tooltips' tabindex='0'>&nbsp;~ <span> Неопределенное количество</span></span>" : "Доступно на складе:&nbsp;<span id='number_item_$price_item->id' class='price_item'>". $price_item->amount ."</span>&nbsp;шт") ?>
                                    </p></th>
                            </tr>
                            </tbody>
                        </table>


                    </div>
                </div>
            <?php } ?>

        <?php } else { ?>
            <p class="text-sm">Нет в наличии</p>
        <?php } ?>
        <div class="gap gap-small"></div>
            <?php if(!empty($part_obj->rating)):?>
                <div class="well product-rating" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
                    <p><input id="rating"  class="kv-ltr-theme-fa-star rating-loading" type="number" value="<?=$part_obj->rating?>"  <?=in_array(Request::$client_ip, explode(',', $part_obj->ip)) ? 'readonly="readonly"' : ''?>  data-min="0" data-max="5" data-step="1" data-size="xs" data-show-clear="false" data-show-caption="false" data-id="<?=$part_obj->id?>" data-factory="Part"/></p>
                    <p>(<span class="dist votes" itemprop="ratingCount"><?=$part_obj->votes?></span> голосов, оценка <span class="dist rating" itemprop="ratingValue"><?=round($part_obj->rating, 2)?></span> из <span itemprop="bestRating">5</span>)</p>
                </div>
            <?php endif;?>
    </div>
    </div>
</div>

<div class="gap"></div>
<span class="block widget-title">Аналоги и заменители</span>


<?php if (!empty($groups)) { ?>
    <div class="row" data-gutter="15">
        <?php foreach ($groups as $group): ?>
            <?php if (count($group[1]) > 0): ?>
                <?php foreach ($group[1] as $key => $part) : ?>
                    <?php $price_item = $part['price_items'][0]; ?>
                    <?php $tecdoc = ORM::factory('Tecdoc');
                    $part_tecdoc = $tecdoc->get_part($price_item->part->tecdoc_id);
                    ?>
                    <div class="col-md-3">
                        <a class="product-link"
                           href="<?= URL::site('katalog/produkt/' . Htmlparser::transliterate($part_tecdoc['brand'] . "-" . $part_tecdoc['article_nr'] . "-" . substr($part_tecdoc['description'], 0, 50)) . "-" . $price_item->part->id); ?>">
                        <div class="product product-sm-left ">

                            <div class="product-img-wrap">
                                <?php if (!empty($price_item->part->tecdoc_id)) :
                                    $graphics = $tecdoc->get_graphics($price_item->part->tecdoc_id);
                                    if(is_array($graphics)):
                                        foreach ($graphics as $img) : ?>
                                            <img class="product-img" src="<?= URL::base(); ?>image/tecdoc_images/<?= $img['image']; ?>" alt="" />
                                        <?php endforeach; ?>
                                    <?php else:?>
                                        <img class="product-img" src="<?= URL::base(); ?>image/tecdoc_images/<?= $graphics['image']; ?>" alt="" />
                                    <?php endif ?>
                                <?php else: ?>
                                    <img class="product-img" src="<?= URL::base(); ?>media/img/no-image.png" alt="" />
                                <?php endif; ?>
                            </div>

                            <div class="product-caption">
                                <span class="bold product-caption-title"><?= $price_item->part->get_brand() ?> <?= $price_item->part->article_long ?> <?= Article::shorten_string($price_item->part->name, 3) ?></span>
                                <div class="product-caption-price"><span
                                        class="product-caption-price-new"><?= $price_item->get_price_for_client() ?>
                                        грн</span>
                                </div>
                                <ul class="product-caption-feature-list">
                                    <li><?= ($price_item->amount == '1' || $price_item->amount == '2' || empty($price_item->amount) ? "Заканчивается" : "Есть в наличии") ?></li>
                                </ul>
                            </div>
                        </div>
                        </a>
                    </div>

                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php } ?>
    <div class="gap"></div>
    <div class="tabbable product-tabs">
        <ul class="nav nav-tabs" id="tabs">
            <?php if (!empty($criterias)) { ?>
                <li><a href="#tabs-2" data-toggle="tab">Характеристики</a></li><?php } ?>
            <?php if (!empty($applied_to)) { ?>
                <li><a href="#tabs-3" data-toggle="tab">Применяемость</a></li><?php } ?>
        </ul>
        <div class="tab-content">
            <?php if (!empty($criterias)) { ?>
                <div class="tab-pane fade" id="tabs-2">
                    <table class="table ">
                        <tbody>
                        <?php foreach ($criterias as $criteria): ?>
                            <tr>
                                <td><?= $criteria['description'] ?></td>
                                <td><?= $criteria['value'] ?></td>
                            </tr>
                        <?php endforeach; ?>
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
                        <?= $applied_to_item['brand'] ?> <?= $applied_to_item['model'] ?> <?= $applied_to_item['description'] ?>
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
<div class="gap"></div>
<?php } ?>
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
