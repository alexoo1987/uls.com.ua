

<?php if (!empty($items)) { ?>
    <header class="page-header">
        <h1 class="page-title">Моя корзина</h1>
    </header>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-condensed shop table-shopping-cart">
                    <thead>
                    <tr>
                        <th>Название</th>
                        <th>Бренд</th>
                        <th>Артикул</th>
                        <th>Цена с учетом <br>скидки</th>
                        <th>Кол-во</th>
                        <th>Кол-во <br>на складе</th>
                        <th>Итого</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sum = 0;
                    foreach ($items as $item) : ?>
                        <tr class="time_th" style="text-align: center">
                            <td class="table-shopping-cart-title"><?= $item['priceitem']->part->name ?></td>
                            <td><?= $item['priceitem']->part->brand_long ?></td>
                            <td><?= $item['priceitem']->part->article_long ?></td>
                            <td><span class="price_position"><?= $item['priceitem']->get_price_for_client() ?></span> грн.</td>
                            <td>
                                <button data-priceitem="<?= $item['id'] ?>" class="product-page-qty product-page-qty-minus">-</button>
                                <input class="product-page-qty product-page-qty-input" disabled type="text" value="<?= $item['qty'] ?>" />
                                <button data-priceitem="<?= $item['id'] ?>" class="product-page-qty tooltips product-page-qty-plus">+<span> Выбрано максимальное количество</span></button>
                            </td>
                            <td><span class="price_item"><?= $item['number'] ?></span> </td>
                            <td><span class="all_price_position"><?= $item['priceitem']->get_price_for_client() * $item['qty'] ?></span> грн.</td>
                            <?php $sum += $item['priceitem']->get_price_for_client() * $item['qty']; ?>
                            <td>
                                <a class="fa fa-close table-shopping-remove delete_row"
                                   data-href="<?= URL::site("cart/remove"); ?>?cart_id=<?= $item['id'] ?>"></a></td>
                        </tr>
                    <?php endforeach; ?>
                        <tr style="text-align: center">
                            <td>Итого</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><span><span class="final_price"><?= $sum ?></span> грн</span></td>
                            <td></td>
                        </tr>
                    <tr style="text-align: center">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><a class="btn btn-primary" href="<?= URL::site("orders/add"); ?>" rel="nofollow">11Купить</a></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
                <div class="gap gap-small"></div>
            </div>
        </div>
        <div class="col-md-12">
<!--            <ul class="shopping-cart-total-list">-->
<!--                <li><span>Итого</span><span><span class="final_price">--><?//= $sum ?><!--</span> грн</span>-->
<!--                </li>-->
<!--            </ul>-->
<!--            <a class="btn btn-primary" href="--><?//= URL::site("orders/add"); ?><!--" rel="nofollow">Купить</a>-->
            <?php $url_previous = $_SERVER['HTTP_REFERER']; ?>
            <a class="btn btn-default" href="<?= $url_previous ?>">Продолжить покупки</a>
<!--            --><?//= URL::site("catalog"); ?>
<!--            <a href="javascript:history.back()">Назад</a>-->
        </div>
    </div>
    <ul class="list-inline">
        <li>
        </li>
    </ul>
<?php } else { ?>
    <div class="text-center"><i class="fa fa-cart-arrow-down empty-cart-icon"></i>
        <p class="lead">Ваша корзина пуста</p><a class="btn btn-primary btn-lg" href="<?= URL::site("katalog"); ?>">Начать покупки <i class="fa fa-long-arrow-right"></i></a>
    </div>
    <div class="gap"></div>
<?php } ?>


