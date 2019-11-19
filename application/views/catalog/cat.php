<?php echo View::factory('common/car_select')->render(); ?>

<div class="flex">
    <?php $count = count(array_unique(array_column($manufacturers, 'manuf_name')))/4;?>
    <?php $unique_brand = ""; ?>
    <?php $i = 0; ?>

    <div class="container flex_cat1">
        <h1 class="widget-title-lg"><?= $content_catalog->h1 ?></h1>
        <?php if(!$manufacturers) :?>
            <p>Модели отсутствуют.</p>
        <?php else:?>
            <ol class="breadcrumb page-breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#">
                <li typeof="v:Breadcrumb"><a href="<?= URL::base()?>" rel="v:url" property="v:title">Интернет магазин автозапчастей</a>
                </li>
                <li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog')?>" rel="v:url" property="v:title">Каталог</a>
                </li>
                <?php if ($category->get_parent()) {?>
                    <li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog') . '/' .  $modification_url . $category->get_parent()->slug?>" rel="v:url" property="v:title"><?= $category->get_parent()->name ?></a></li>
                <?php } ?>

                <li class="active" typeof="v:Breadcrumb"><a href="<?= URL::site('katalog').'/'.$category->slug ?>" rel="v:url" property="v:title"><?= $category->name ?></a></li>
            </ol>
            <div class="">
                <div class="masonry-layout">
                    <?php foreach ($manufacturers as $manufacturer):?>
                        <?php if($unique_brand != $manufacturer['manuf_name']): ?>
                            <?php if($unique_brand != ""): ?>
                                            </ul>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="masonry-layout__panel">
                                <div class="masonry-layout__panel-content">
                                    <ul class="no-styled-ul new_manuf_list catalog-list">
                                        <li>
                                            <a href="<?=URL::site('katalog/'.$manufacturer['manuf_url'].'/'.$slug);?>"><span class="dist"><?=$manufacturer['manuf_name']?></span></a>
                                        </li>
                                        <ul class="in_new_manuf_list">
                                            <li class="inline">
                                                <a href="<?=URL::site('katalog/'.$manufacturer['manuf_url'].'/'.$manufacturer['model_url'].'/'.$slug);?>"><span class="dist"><?=$manufacturer['model_name']?></span></a>
                                            </li>
                                            <?php $unique_brand = $manufacturer['manuf_name']; ?>
                                            <?php else: ?>
                                                <li class="inline">
                                                    <a href="<?=URL::site('katalog/'.$manufacturer['manuf_url'].'/'.$manufacturer['model_url'].'/'.$slug);?>"><span class="dist"><?=$manufacturer['model_name']?></span></a>
                                                </li>
                                            <?php endif; ?>
                                            <?php $i++; ?>
                                            <?php if($i == count($manufacturers)): ?>
                                        </ul>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="flex_div category container flex_cat2">

        <!--        TOP PRODUCTS -->
        <div class="row parts_blocks" data-gutter="15">
            <div class="flex_div category">
                <?php foreach ($topParts as $part) : ?>
                    <?php if($part['price_start']<=1) {continue;} ?>
                    <div class="col-md-4">
                        <img class="find_line" src="/images/1.png">
                        <div class="product">
                            <div class="product-img-wrap">

                                <a class="product-link"
                                   href="<?= $url = Helper_Url::getPartUrl($part, [], true) ?>">
                                    <?php if (!empty($part['images'])) :
                                        ?>
                                        <img class="product-img-primary" alt="<?= $part['brand_long'] ?> <?= $part['article_long'] ?> <?= substr($part['name'], 0, 50) ?>" title="<?= $part['brand_long'] ?> <?= $part['article_long'] ?> <?= substr($part['name'], 0, 50) ?>" src="<?= URL::base(); ?>image/tecdoc_images<?= $part['images']; ?>" />

                                        <img class="product-img-alt" src="<?= URL::base(); ?>image/tecdoc_images<?= $part['images']; ?> " />

                                    <?php else: ?>
                                        <img class="product-img-primary" src="<?= URL::base(); ?>media/img/no-image.png" alt="<?= $part['brand_long'] ?> <?= $part['article_long'] ?> <?= substr($part['name'], 0, 50) ?>" title="<?= $part['brand_long'] ?> <?= $part['article_long'] ?> <?= substr($part['name'], 0, 50) ?>" itemprop="image"/>
                                        <img class="product-img-alt" src="<?= URL::base(); ?>media/img/no-image.png" itemprop="image"/>
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
                                                        echo "<span class='tooltips' tabindex='0'>~ <span> Приблизительный срок доставки</span></span>".$part['delivery']." дн.";
                                                    }
                                                    else{
                                                        if($part['delivery'] > 0)
                                                        {
                                                            echo $part['delivery']." дн.";
                                                        }
                                                        else
                                                        {
                                                            echo "1 дн.";
                                                        }
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
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <!--        END TOP -->
    </div>

    <div class="seo_text flex_cat3">
        <?= $seo_data->content ?>
    </div>
<!--    <div class="category-video flex_cat3" style="margin: 20px auto">-->
<!--        --><?//= $category->video ?>
<!--    </div>-->

</div>
</div>
<div class="col-md-12" style="margin-top: 5px">
    <?php echo $content_catalog->content; ?>
</div>











