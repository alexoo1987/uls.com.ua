

<div class="modal fade" id="one-click" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?= Form::open('', array('class' => 'form-horizontal form-one-click', 'id' => 'validate_form',)); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <span class="block modal-title" id="myModalLabel">Моя корзина</span>
            </div>

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
                                    <th>Итого</th>
                                </tr>
                                </thead>
                                <tbody>



                                <?php
                                $sum = 0;
                                foreach ($items as $item) : ?>
                                    <input type="hidden" name="id" value="<?= $item['priceitem']->id ?>">
                                    <tr class="time_th" style="text-align: center">
                                        <td class="table-shopping-cart-title"><?= $item['priceitem']->part->name ?></td>
                                        <td><?= $item['priceitem']->part->brand_long ?></td>
                                        <td><?= $item['priceitem']->part->article_long ?></td>
                                        <td><span class="price_position"><?= $item['priceitem']->get_price_for_client() ?></span> грн.</td>
                                        <td>
                                            <?= $item['qty'] ?>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>



                            </table>
                            <div class="gap gap-small"></div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal-footer" style="text-align:center">
                <div class="container modal-one-click">

                    <div class="form-group">

                        <?= Form::label('phone', 'Телефон*', array('class' => 'control-label ')); ?>

                            <?php echo Form::input('phone', '', array('class'=>'form-control phone'))?>

                    </div>

                    <div class="form-group" >
                        <div class="controls">
                            <?= Form::submit('create', 'Оформить заказ', array('class' => 'btn btn-primary')); ?>
                        </div>
                    </div>

                </div>

            </div>

            <?= Form::close(); ?>

        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('#one-click').modal('show');

        $('.form-one-click').submit(function (e) {
            e.preventDefault();
            var el=$(this);
            var data =el.serialize();
            $.post('/orders/add_one_click', data, function (res) {
                console.log(res);
                window.location.href = res;
            });
        });

    });

</script>

