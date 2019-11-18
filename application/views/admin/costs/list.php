<script>
    var costs_ok_url = '<?=URL::site('admin/costs/costs_arhive');?>';
</script>
<div class="container">
    <?php if (ORM::factory('Permission')->checkPermission('costs_manage')) { ?>
        <div class="container">
            <div class="span3">
                <?php if ($_SERVER['REQUEST_URI'] == '/admin/costs/list'): ?>
                    <form method="POST">
                        <div class="form-group">
                            <label for="type">Категория</label>
                            <select name="type" id="type">
                                <?php foreach ($costs_type AS $one) { ?>
                                    <option value="<?= $one['id'] ?>"><?= $one['type'] ?></option>
                                <?php } ?>
                            </select>
                            <label id="lable_for_supplier" for="supplier">Поставщик</label>
                            <select name="supplier" id="supplier">
                                <?php foreach ($suppliers AS $one=>$key) { ?>
                                    <option value="<?= $one ?>"><?= $key ?></option>
                                <?php } ?>
                            </select>
                            <label for="amount">Внесено (грн.)</label>
                            <input type="number" class="form-control" id="amount" name="amount" required>
        <!--                    <label for="date">Дата</label>-->
        <!--                    <input type="text" class="form-control datepicker" id="date" name="date" required>-->
                            <label for="comment">Комментарий</label>
                            <textarea class="form-control" id="comment" name="comment"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Добавить</button>
                    </form>
                <?php endif ?>
            </div>
            <div class="span8">
                <?php if ($_SERVER['REQUEST_URI'] == '/admin/costs/list'): ?>
                    <div class="alert alert-info">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        Данный раздел позволяет фиксировать в системе денежные движения<br/>
                        1. Чтобы вычесть сумму из баланса сотрудника необходимо просто ввести число, напр,
                        <strong>500</strong><br/>
                        2. Чтобы добавить сумму к балансу сотрудника необходимо ввести число с знаком "-", напр,
                        <strong>-500</strong>
                    </div>
                    <div class="alert alert-info">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong>Внимание!</strong> Затраты, добавленные с отрицательным знаком, не будут расспределены между
                        заказами!
                    </div>
                <?php endif ?>
            </div>
        </div>
        <div class="container">
            <?= Form::open('', array('class' => 'form-horizontal supplier_balance', 'method' => 'get')); ?>
            <div class="flex_box">
                <div class="in_flex_25">Дата от<br /><?= Form::input('date_from', HTML::chars(Arr::get($filters, 'date_from')), array('class' => 'datepicker dateFrom')); ?> </div>
                <div class="in_flex_25">Дата до<br /><?= Form::input('date_to', HTML::chars(Arr::get($filters, 'date_to')), array('class' => 'datepicker')); ?></div>
                <div class="in_flex_25">Сотрудник: <?= Form::select('user_id', $users, Arr::get($filters, 'user_id')); ?></div>
                <div class="in_flex_25">
                    <br />
                    <?= Form::submit('', 'Применить фильтр', array('class' => 'btn btn-primary white')); ?>
                    <a href="<?=URL::site('admin/costs/list');?>" class="btn btn-primary white"><i class="icon-white icon-refresh"></i> Сброс</a>
                </div>
            </div>
            <?= Form::close(); ?>
        </div>
        <?php if ($_SERVER['REQUEST_URI'] == '/admin/costs/list'): ?>
            <a href="<?= URL::site('/admin/costs/list_archive'); ?>" class="btn btn-primary">Архив затрат</a>
        <?php else: ?>
            <a href="<?= URL::site('/admin/costs/list'); ?>" class="btn btn-primary">Все затраты</a>
        <?php endif ?>
        <a href="#" class="btn btn-primary" id="costs_ok"><i class="icon-white icon-thumbs-up"></i>Отправить в архив</a>
        <p></p>
        <table class="table table-striped table-bordered">
            <tr>
                <th><input type="checkbox" id="select_all_cost" /></th>
                <th>Категория</th>
                <th>Поставщик</th>
                <th>Дата</th>
                <th>Сотрудник</th>
                <th>Архив</th>
                <th>Сумма (грн.)</th>
                <th>Комментарий</th>
                <th></th>
            </tr>
            <?php $sum_costs = 0; $states_arr = array(1,2,3,26)?>
            <?php foreach ($costs as $key => $cost) : ?>
                <tr>
                    <?php
//                    if(in_array( $cost['type'], $states_arr)){
                        $sum_costs += $cost['amount'];
//                    }
                    ?>
                    <td><input type="checkbox" class="order_checkbox" data-id="<?=$cost['id']?>" /></td>
                    <td><?= DB::select('type')->from('costs_type')->where('id', '=', $cost['type'])->execute()->as_array()[0]['type'] ?></td>
                    <td><?= $cost['supplier_id'] !=0 ? ORM::factory('supplier')->where('id', '=', $cost['supplier_id'])->find()->name : '' ?></td>
                    <td><?= $cost['date'] ?></td>
                    <td><?= ORM::factory('user')->where('id', '=', $cost['user_id'])->find()->surname ?></td>
                    <td><?= $cost['arhive'] == 1 ? "Да" : "Нет" ?></td>
                    <td><?= $cost['amount'] ?></td>
                    <td><?= $cost['comment'] ?></td>
                    <td>
                        <?php if(in_array(Auth::instance()->get_user()->id, [74, 2])) : ?>
                            <a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/costs/costs_delete/'.$cost['id']);?>"><i class="icon-remove"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td></td>
                <td>Всего разнести:</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><?= $sum_costs;?> грн</td>
                <td></td>
            </tr>
        </table>
    <?php } ?>
</div>