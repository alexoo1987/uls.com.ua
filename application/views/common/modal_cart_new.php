


<?php if (!empty($items)) { ?>
<div class="modal-body">
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
                                   data-href="<?= URL::site("ajax/modal_cart_remove"); ?>?cart_id=<?= $item['id'] ?>"></a>
                            </td>
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

                </table>
                <div class="gap gap-small"></div>
            </div>

        </div>
    </div>
</div>
    <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Продолжить покупки</button>
        <a class="btn btn-primary" style="width: 155px;" href="<?= URL::site("orders/add"); ?>" rel="nofollow">Купить</a>
    </div>
<!--    <p style="color: red; text-align: center; text-transform: uppercase; font-weight: bold;">Заказ товара при полной предоплате</p>-->
<?php } else { ?>
    <div class="modal-body">
    <div class="text-center"><i class="fa fa-cart-arrow-down empty-cart-icon"></i>
        <p class="lead">Ваша корзина пуста</p><a class="btn btn-primary btn-lg" href="<?= URL::site("katalog"); ?>">Начать покупки <i class="fa fa-long-arrow-right"></i></a>
    </div>
    <div class="gap"></div>
    </div>

<?php } ?>


<script type="text/javascript">
    var minus_all = [];
    minus_all = document.getElementsByClassName('product-page-qty-input');
    for(var i=0;i<minus_all.length;i++){
        var value = parseInt($(".product-page-qty-input:eq("+i+")").val(), 10);
        if(value==1){
            $(".product-page-qty-minus:eq("+i+")").attr('disabled', 'disabled');
        }
        console.log (value);
    }
    $(document).on("click", ".add-to-cart", function () {
        $('.modal-one-click').hide()

    });
    $('.delete_row').on('click', function(){
        var url = $(this).data('href');
        var qty = $('.product-page-qty-input').val();
        var priceitem = $(this).data('priceitem');
        var qty = $('#qty_' + priceitem).val();

        $.ajax({
            url: url,
            type: 'POST',
            success: function(data) {
                $('#bottom').html(data)
            }
        });
        $.ajax({
            url: '/ajax/get_number_cart',
            type: 'POST',
            success: function(data) {
                data = JSON.parse(data);
                $('qty.cart').html(data.cart_count);
                console.log(data.cart_count);
            }
        });
    });


    //plus on cart page
    $(".table.shop .product-page-qty-plus").on('click', function() {

        var input = $(this).closest('.time_th').find('.price_item').text();
        var currentVal = parseInt(jQuery(this).prev(".product-page-qty-input").val(), 10);
        if (!currentVal || currentVal == "" || currentVal == "NaN") currentVal = 0;
        console.log(currentVal);
        console.log(input);

        if((currentVal==input-1)&&(input != "В наличии"))
        {
            $(this).closest('.time_th').find('.product-page-qty-plus').attr('disabled', 'disabled');
        }
        jQuery(this).prev(".product-page-qty-input").val(currentVal + 1);

        $(this).closest('.time_th').find('.product-page-qty-minus').removeAttr('disabled');

        currentVal = parseInt(jQuery(this).prev(".product-page-qty-input").val(), 10);
        var price_position = parseInt($(this).closest('.time_th').find('.price_position').text(), 10);
        var all_price_position = parseInt($(this).closest('.time_th').find('.all_price_position').text(), 10);
        $(this).closest('.time_th').find('.all_price_position').text(price_position + all_price_position);

        var sum=0;
        $(".all_price_position").each(function() {
            sum += parseInt($(this).text(), 10);
        });
        $('.final_price').text(sum);


        var priceitem = $(this).data('priceitem');
        var url = '/ajax/add_to_cart';
        //console.log(priceitem);
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                qty: 1,
                priceitem_id: priceitem,
                //number: number,
            },
            success: function(data) {
                //data = JSON.parse(data);
                $('qty.cart').html(data.cart_count);
            }
        });




    });

    var all_number_in_shop_cart = [];
    var all_qty_in_shop_cart = [];
    var all_qty_in_shop_cart_plus = [];
    all_qty_in_shop_cart = document.getElementsByClassName('product-page-qty-input');
    all_number_in_shop_cart = document.getElementsByClassName('price_item');
    all_qty_in_shop_cart_plus = document.getElementsByClassName('product-page-qty-plus');
    for(var i=0;i<all_number_in_shop_cart.length;i++) {
        if (all_qty_in_shop_cart[i].value == all_number_in_shop_cart[i].innerHTML) {
            //$(".product-page-qty-plus:eq("+i+")").attr('disabled', 'disabled');
            all_qty_in_shop_cart_plus[i].disabled = true;
        }
    }

    $(".table.shop .product-page-qty-minus").on('click', function() {
        var input = $(this).closest('.time_th').find('.price_item').text();
        var currentVal = parseInt($(this).next(".product-page-qty-input").val(), 10);

        if (currentVal == "NaN") currentVal = 1;
        if (currentVal > 1) {
            $(this).next(".product-page-qty-input").val(currentVal - 1);
            //currentVal = parseInt(jQuery(this).prev(".product-page-qty-input").val(), 10);
            var price_position = parseInt($(this).closest('.time_th').find('.price_position').text(), 10);
            var all_price_position = parseInt($(this).closest('.time_th').find('.all_price_position').text(), 10);
            $(this).closest('.time_th').find('.all_price_position').text(all_price_position-price_position);

            if(currentVal-1 == 1)
            {
                $(this).attr('disabled', 'disabled');
            }

            var sum=0;
            $(".all_price_position").each(function() {
                sum += parseInt($(this).text(), 10);
            });
            $('.final_price').text(sum);
        }

        if(currentVal<=input)
        {
            $(this).closest('.time_th').find('.product-page-qty-plus').removeAttr('disabled');
        }
        //jQuery(this).prev(".product-page-qty-input").val(currentVal + 1);
        var priceitem = $(this).data('priceitem');
        var url = '/ajax/delete_from_cart';
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                qty: 1,
                priceitem_id: priceitem,
                //number: number,
            },
            success: function(data) {
                //data = JSON.parse(data);
                $('qty.cart').html(data.cart_count);
            }
        });
    });
</script>

