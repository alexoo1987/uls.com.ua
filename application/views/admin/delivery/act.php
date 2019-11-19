<div class="container">
    <div class="row">
<!--        <div class="span6 offset3">-->
            <? if ($message) : ?>
                <h3 class="alert alert-info">
                    <?= $message; ?>
                </h3>
            <? endif; ?>

            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <td><input id="select_all" type="checkbox"></td>
                    <td>Номер заказа</td>
                    <td>Артикул</td>
                    <td>Бренд</td>
                    <td>Кол-во</td>
                    <td>Сумма</td>
                    <td>Клиент</td>
                    <td>Адресс доставки</td>
                    <td>Комментарий менеджера</td>
                </tr>
                </thead>
                <tbody>
                <?= Form::open(URL::site('admin/delivery/get_act_excel_client'), array('class' => 'form-horizontal')); ?>
                <?php foreach ($results as $result): ?>
                    <?php if($result['ready_order'] == 1 OR $result['ready_order'] == 2){$style = "background: rgb(46, 199, 46)";} else{$style = "background: white";} ?>
                <tr>
                    <td style="<?=$style ?>"><?= Form::checkbox('ids[]', (integer)$result['position_id'], FALSE, array('class' => 'order_checkbox')); ?></td>
                    <td style="<?=$style ?>"><a href="<?=URL::site('admin/orders/items/'.$result['order_id']);?>"><?=$result['order_id'] ?></a></td>
                    <td style="<?=$style ?>"><?=$result['article'] ?></td>
                    <td style="<?=$style ?>"><?=$result['brand'] ?></td>
                    <td style="<?=$style ?>"><?=$result['amount'] ?></td>
                    <td style="<?=$style ?>"><?=$result['sale_per_unit'] ?> грн.</td>
                    <td style="<?=$style ?>"><?=$result['name']." ".$result['surname']."<br>".$result['phone']; ?></td>
                    <td style="<?=$style ?>"><?=$result['delivery_address'] ?></td>
                    <td style="<?=$style ?>"><?=$result['manager_comment'] ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <div class="control-group">
                        <div class="controls">
                            <?= Form::submit('create', 'Получить акт доставки по клиентам', array('class' => 'btn btn-primary')); ?>
                        </div>
                    </div>
                </tr>
                </tbody>

                <?= Form::close(); ?>
            </table>
    </div>
</div>