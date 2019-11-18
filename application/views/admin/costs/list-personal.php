<script>
    var costs_ok_url = '<?=URL::site('admin/costs/costs_personal_arhive');?>';
</script>
<div class="container">
    <?= Form::open('', array('class' => 'form-horizontal supplier_balance', 'method' => 'get')); ?>
    <div class="flex_box">
        <div class="in_flex_25">Дата от<br /><?= Form::input('date_from', HTML::chars(Arr::get($filters, 'date_from')), array('class' => 'datepicker dateFrom', 'data-from' => '2018-02-21')); ?> </div>
        <div class="in_flex_25">Дата до<br /><?= Form::input('date_to', HTML::chars(Arr::get($filters, 'date_to')), array('class' => 'datepicker')); ?></div>
        <div class="in_flex_25">Тип<br /><?= Form::select('type', $types, Arr::get($filters, 'type'));; ?></div>
        <div class="in_flex_25">
            <br />
            <?= Form::submit('', 'Применить фильтр', array('class' => 'btn btn-primary white')); ?>
            <a href="<?=URL::site('admin/costs/personal_costs');?>" class="btn btn-primary white"><i class="icon-white icon-refresh"></i> Сброс</a>
        </div>
    </div>
    <?= Form::close(); ?>

    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#personal">Затраты</a></li>
        <li><a data-toggle="tab" href="#static">Статические затраты (разносятся)</a></li>
    </ul>

    <div class="tab-content">
        <div id="personal" class="tab-pane fade in active">
            <a href="#" class="btn btn-primary" id="costs_ok"><i class="icon-white icon-thumbs-up"></i>Отправить в архив</a>
            <?php $url = $_SERVER['REQUEST_URI']."&all_costs=1"; ?>
            <a href="<?= URL::site($url); ?>" class="btn btn-warning"><i class="icon-white icon-thumbs-up"></i>Отправить все в архив</a>
            <p></p>

            <?= Form::open('', array('class' => 'form-horizontal supplier_balance', 'method' => 'post')); ?>
            <div class="flex_box">
                <div class="in_flex_25">Комметарий<br /><?= Form::input('comment', HTML::chars(Arr::get($data, 'comment'))); ?> </div>
                <div class="in_flex_25">Сумма<br /><?= Form::input('amount', HTML::chars(Arr::get($data, 'amount'))); ?></div>
                <div class="in_flex_25">Дата<br /><?= Form::input('created', HTML::chars(Arr::get($data, 'created')), array('class' => 'datepicker', 'data-from' => '2018-02-21')); ?></div>
                <div class="in_flex_25">
                    <br />
                    <?= Form::submit('', 'Добавить', array('class' => 'btn btn-primary white')); ?>
                </div>
            </div>
            <?= Form::close(); ?>
            <table class="table table-striped table-bordered">
                <tr>
                    <th><input type="checkbox" id="select_all_cost" /></th>
                    <th>Дата</th>
                    <th>Сотрудник</th>
                    <th>Комметарий</th>
                    <th>Тип</th>
                    <th>Архив</th>
                    <th>Сумма (грн.)</th>
                    <th></th>
                </tr>
                <?php foreach ($costs_personal as $cost_personal) : ?>
                    <tr>
                        <td><input type="checkbox" class="order_checkbox" data-id="<?=$cost_personal->id?>" /></td>
                        <td><?= $cost_personal->created ?></td>
                        <td><?= $cost_personal->user->name ?></td>
                        <td><?= $cost_personal->comment ?></td>
                        <td><?= $cost_personal->type == 1 ? "Мои затраты" : "Разносятся" ?></td>
                        <td><?= $cost_personal->arhive == 1 ? "Да" : "Нет" ?></td>
                        <td><?= $cost_personal->amount ?></td>
                        <td>
                            <?php if($cost_personal->type == 1): ?>
                                <a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/costs/personal_costs_delete/'.$cost_personal->id);?>"><i class="icon-remove"></i></a>
                                <a role="button" data-toggle="modal" class="btn btn-mini btn delete_row" href="#personal_cost<?=$cost_personal->id?>"><i class="icon-edit"></i></a>

                                <!-- Modal -->
                                <div id="personal_cost<?=$cost_personal->id?>" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        <h3>Редактировать затрату</h3>
                                    </div>
                                    <div class="modal-body">
                                        <?= Form::open(URL::site('admin/costs/edit_personal'), array('class' => 'form-horizontal')); ?>

                                        <div class="control-group">
                                            <?= Form::label('comment', 'Комментарий', array('class' => 'control-label')); ?>
                                            <div class="controls">
                                                <?= Form::input('comment',$cost_personal->comment); ?>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <?= Form::label('created', 'Дата', array('class' => 'control-label')); ?>
                                            <div class="controls">
                                                <?= Form::input('created',$cost_personal->created, array('class' => 'datepicker')); ?>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <?= Form::label('amount', 'Создать номер заказа поставцика', array('class' => 'control-label')); ?>
                                            <div class="controls">
                                                <?= Form::input('amount', $cost_personal->amount); ?>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <div class="controls">
                                                <?= Form::hidden('id', $cost_personal->id , array('validate' => 'required')); ?>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <div class="controls">
                                                <?= Form::submit('create', 'Обновить', array('class' => 'btn btn-primary save_item')); ?>
                                            </div>
                                        </div>
                                        <?= Form::close(); ?>
                                    </div>
                                </div>
                                <!--END Modal -->
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td></td>
                    <td>Всего:</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><?= $totalCost ?> грн.</td>
                    <td></td>
                </tr>
            </table>
            <?= $pagination ?>
        </div>
        <div id="static" class="tab-pane fade">
            <?= Form::open(URL::site('admin/costs/personal_costs_static'), array('class' => 'form-horizontal supplier_balance', 'method' => 'post')); ?>
            <div class="flex_box">
                <div class="in_flex_25">Комметарий<br /><?= Form::input('comment', HTML::chars(Arr::get($data, 'comment'))); ?> </div>
                <div class="in_flex_25">Сумма<br /><?= Form::input('amount', HTML::chars(Arr::get($data, 'amount'))); ?></div>
                <div class="in_flex_25">
                    <br />
                    <?= Form::submit('', 'Добавить', array('class' => 'btn btn-primary white')); ?>
                </div>
            </div>
            <?= Form::close(); ?>
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Дата</th>
                    <th>Сотрудник</th>
                    <th>Комметарий</th>
                    <th>Сумма (грн.)</th>
                    <th></th>
                </tr>
                <?php foreach ($costs_static as $cost_personal) : ?>
                    <tr>
                        <td><?= $cost_personal->created ?></td>
                        <td><?= $cost_personal->user->name ?></td>
                        <td><?= $cost_personal->comment ?></td>
                        <td><?= $cost_personal->amount ?></td>
                        <td>
                            <a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/costs/personal_costs_static_delete/'.$cost_personal->id);?>"><i class="icon-remove"></i></a>
                            <a role="button" data-toggle="modal" class="btn btn-mini btn delete_row" href="#static_cost<?=$cost_personal->id?>"><i class="icon-edit"></i></a>

                            <!-- Modal -->
                            <div id="static_cost<?=$cost_personal->id?>" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h3>Редактировать затрату</h3>
                                </div>
                                <div class="modal-body">
                                    <?= Form::open(URL::site('admin/costs/edit_static'), array('class' => 'form-horizontal')); ?>

                                    <div class="control-group">
                                        <?= Form::label('comment', 'Комментарий', array('class' => 'control-label')); ?>
                                        <div class="controls">
                                            <?= Form::input('comment',$cost_personal->comment); ?>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <?= Form::label('amount', 'Создать номер заказа поставцика', array('class' => 'control-label')); ?>
                                        <div class="controls">
                                            <?= Form::input('amount', $cost_personal->amount); ?>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="controls">
                                            <?= Form::hidden('id', $cost_personal->id , array('validate' => 'required')); ?>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="controls">
                                            <?= Form::submit('create', 'Обновить', array('class' => 'btn btn-primary save_item')); ?>
                                        </div>
                                    </div>
                                    <?= Form::close(); ?>
                                </div>
                            </div>
                            <!--END Modal -->
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>

        </div>
    </div>
</div>