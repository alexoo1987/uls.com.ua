<div class="container">
    <?php if((ORM::factory('Permission')->checkPermission('create_penalty'))&&($status_filter==1)) { ?>
        <?= Form::open('', array('class' => 'form-horizontal', 'id' => 'validate_form')); ?>
        <div class="control-group">
            <?= Form::label('user_id', 'Пользователь', array('class' => 'control-label')); ?>
            <div class="controls">
                <?= Form::select('user_id', $managers, null ,array('required' => 'required', 'id' => 'managers')); ?>
            </div>
        </div>

        <div class="control-group">
            <?= Form::label('order_id', 'Заказ', array('class' => 'control-label')); ?>
            <div class="controls">
                <?= Form::select('order_id', null); ?>
            </div>
        </div>

        <div class="control-group">
            <?= Form::label('amount', 'Сумма (грн)', array('class' => 'control-label')); ?>
            <div class="controls">
                <?= Form::input('amount', null, array('required' => 'required')); ?>
            </div>
        </div>

        <div class="control-group">
            <?= Form::label('description', 'Коментарий', array('class' => 'control-label')); ?>
            <div class="controls">
                <?= Form::textarea('description', null, array('rows' => '2')); ?>
            </div>
        </div>

        <div class="control-group">
            <div class="controls">
                <?= Form::submit('create', 'Добавить', array('class' => 'btn btn-primary')); ?>
            </div>
        </div>
        <?= Form::close(); ?>
    <?php } ?>

    <b>Фильтр</b>
    <?= Form::open('', array('class' => 'form-horizontal', 'method' => 'get')); ?>
    Дата
    от <?= Form::input('date_from', HTML::chars(Arr::get($filters, 'date_from')), array('class' => 'datepicker')); ?>
    до <?= Form::input('date_to', HTML::chars(Arr::get($filters, 'date_to')), array('class' => 'datepicker')); ?><br/>
    Пользователь <?= Form::select('user_id', $users, Arr::get($filters, 'user_id')); ?><br />
    <?= Form::submit('', 'Применить фильтр', array('class' => 'btn btn-primary')); ?>
    <?= Form::close(); ?>


    <a href="<?= URL::site('admin/penalty/list'); ?>" class="btn btn-primary"><i class="icon-white icon-refresh"></i>
        Сброс</a><br/><br/>
    <?php $url = $_SERVER['REQUEST_URI']."&status=1"; ?>
    <?php if($status_but==1 AND ORM::factory('Permission')->checkPermission('delete_penalty')): ?>
        <a href="<?= URL::site($url); ?>" class="btn btn-primary"><i class="icon-remove"></i> Отправить все штрафы в архив</a>
    <?php endif ?>
    <table class="table table-striped table-bordered">
        <tr>
            <?php if (ORM::factory('Permission')->checkPermission('delete_penalty')){?>
            <th><?= Form::checkbox('total', '', '', array('id' => 'select_penalty')); ?></th>
            <?php }?>
            <th>Отдел</th>
            <th>Пользователь</th>
            <th>Дата/время</th>
            <th>Заказ</th>
            <th>Артикул</th>
            <th>Сумма (грн)</th>
            <th>Комментарий</th>
            <th>Добавил</th>
            <th>Статус</th>
            <?php if (ORM::factory('Permission')->checkPermission('delete_penalty')) { ?>
            <th></th>
            <?php } ?>
        </tr>
        <?= Form::open('admin/penalty/delete', array('class' => 'form-horizontal', 'id' => 'delete_penalty', 'autocomplete' => 'off')); ?>
        <?php $url_form = $_SERVER['REQUEST_URI'];?>
        <?= Form::input('url', $url_form, array('type' => 'hidden')); ?>
        <?php foreach ($data AS $penalty) { ?>
            <tr>
                <?php if (ORM::factory('Permission')->checkPermission('delete_penalty')){?>
                <td><?= Form::checkbox('delete[]', $penalty->id); ?></td>
                <?php }?>
                <td><?= isset($penalty->role_id) ? DB::select()->from('roles')->where('id', '=', $penalty->role_id)->execute()->as_array()[0]['name'] : '-' ?></td>
                <td><?= isset($penalty->user_id) ? ORM::factory('user')->where('id', '=', $penalty->user_id)->find()->surname : '-' ?></td>
                <td><?= date('d.m.Y H:i:s', strtotime($penalty->date)) ?></td>
                <td><a href="<?= URL::site('admin/orders/items/' . $penalty->order_id); ?>"><?= $penalty->order_id ?></td>
                <td><a href="<?= URL::site('admin/orders/items/?ids=' . $penalty->orderitem_id); ?>"><?= $penalty->orderitem->article ?></td>
                <td><?= $penalty->amount ?></td>
                <td><?= $penalty->description ?></td>
                <td><?= isset($penalty->creator) ? ORM::factory('user')->where('id', '=', $penalty->creator)->find()->surname : '-' ?></td>
                <td><?= $penalty->status ?></td>
                <?php if (ORM::factory('Permission')->checkPermission('delete_penalty')) { ?>
                    <td>
                        <a class="btn btn-mini btn-danger" href="<?=URL::site('admin/penalty/list?delete='.$penalty->id);?>"><i class="icon-remove"></i> Удалить</a>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
        <?php if (ORM::factory('Permission')->checkPermission('delete_penalty')){?>
            <?= Form::submit('archive_all', 'Отправить в архив', array('class' => 'btn btn-success')); ?>&#160;
            <?= Form::submit('delete_all', 'Удалить', array('class' => 'btn btn-success')); ?>
            <br/><br/>
        <?php }?>
        <?= Form::close(); ?>
        <tr>
            <td colspan="5"><b>Сумма:</b></td>
            <td><b><?=$total?></b></td>
            <td></td>
        </tr>
    </table>
    <?=$pagination?>
</div>