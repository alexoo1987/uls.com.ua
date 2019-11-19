<div class="container">
    <div class="row">
        <? if ($message) : ?>
            <h3 class="alert alert-info">
                <?= $message; ?>
            </h3>
        <? endif; ?>

        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home">Возвраты позиций</a></li>
            <li><a data-toggle="tab" href="#menu1">Задания</a></li>
        </ul>

        <div class="tab-content">
            <div id="home" class="tab-pane fade in active">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <td><input id="select_all" type="checkbox"></td>
                        <td>Поставщик</td>
                        <td>Артикулы</td>
                        <td>Сумма</td>
                        <td>Адресс</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?= Form::open(URL::site('admin/delivery/get_act_excel_suppliers'), array('class' => 'form-horizontal')); ?>
                    <?php
                    foreach ($results as $client=>$key):?>
                        <tr>
                            <td><?= Form::checkbox('ids[]', (integer)$client, FALSE, array('class' => 'order_checkbox')); ?></td>
                            <!--                    <td>--><?//= Form::checkbox('ids_cash[]', (integer)$client, FALSE, array('class' => 'order_checkbox')); ?><!--</td>-->
                            <td><?php echo $key[0]['name']?></td>
                            <td>
                                <?php $balance_position = 0;
                                foreach ($key as $arr=>$position){?>
                                    <?php
                                    $order_date_return_max = new DateTime($position['date_time']);
                                    $order_date_return_normal = clone $order_date_return_max;
                                    $order_date_return_normal->add(new DateInterval('P10D'));
                                    $order_date_return_max->add(new DateInterval('P14D'));
                                    $now = new DateTime();

                                    if ($now < $order_date_return_normal) {
                                        $style = "background: #c1fac1;";
                                    } elseif($now >= $order_date_return_normal AND $now <= $order_date_return_max){
                                        $style = "background: #fbff00;";
                                    }
                                    else
                                    {
                                        $style = "background: #ff0000;";
                                    }
                                    ?>
                                    <span style="<?=$style?>"><?= $position['article']." (".$position['amount']." шт)"; ?></span>
                                    <?php $balance_position = $balance_position + $position['purchase_per_unit']*$position['amount'];  ?>
                                <?php }?>
                            </td>
                            <td><?= $balance_position." грн." ?></td>
                            <td><?php echo $key[0]['address'];?></td>
                        </tr>
                    <?php endforeach;?>
                    <tr>
                        <div class="control-group">
                            <div class="controls">
                                <?= Form::submit('create', 'Получить акт доставки по поставщикам', array('class' => 'btn btn-primary')); ?>
                            </div>
                        </div>
                    </tr>
                    </tbody>

                    <?= Form::close(); ?>
                </table>
            </div>

            <div id="menu1" class="tab-pane fade">

                <h3>Задания</h3>

                <?= Form::open(URL::site('admin/delivery/add_task'), array('class' => 'form-horizontal', 'id' => 'validate_form')); ?>
                <div class="control-group">
                    <?= Form::label('comment_text', 'Коментарий', array('class' => 'control-label')); ?>
                    <div class="controls">
                        <?= Form::input('comment_text', HTML::chars(Arr::get($data, 'comment_text'))); ?>
                    </div>
                </div>

                <div class="control-group">
                    <?= Form::label('value', 'Сумма', array('class' => 'control-label')); ?>
                    <div class="controls">
                        <?= Form::input('value', HTML::chars(Arr::get($data, 'value'))); ?>
                        <cpan class="add-on">грн.</cpan>
                    </div>
                </div>

                <div class="control-group">
                    <div class="controls">
                        <?= Form::submit('create', 'Добавить', array('class' => 'btn btn-primary')); ?>
                    </div>
                </div>
                <?= Form::close(); ?>


                <?php if(!empty($results_tsks)): ?>
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <!--                    <td>Пользователь</td>-->
                            <td>Дата</td>
                            <td>Комментарий</td>
                            <td>Сумма</td>
                            <td></td>
                        </tr>
                        </thead>
                        <tbody>

                        <?php foreach ($results_tsks as $task): ?>
                            <tr>
                                <!--                    <td>--><?//= $task['surname'] ?><!--</td>-->
                                <td><?= $task['data'] ?></td>
                                <td><?= $task['text'] ?></td>
                                <td><?= $task['value']." грн" ?>
                                <td><a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/delivery/delete_task?id='.$task['id']);?>"><i class="icon-remove"></i></a></td>
                            </tr>
                        <?php endforeach; ?>

                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>